<?php

namespace Fd\HslBundle\Event\Listener;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Event\Event;

/**
 * Custom listener that modify an image after upload.
 */
class HslImageUploadListener
{
    const DEFAULT_RESIZE_WHEN_WIDTH_EXCEED = 1024;
    const DEFAULT_RESIZE_TO_WIDTH = 1024;
    const DEFAULT_QUALITY = 90;

    private $config;
    /**
     * @var ImageManager $imageManager
     */
    private $imageManager;

    /**
     * @param array $config config value from 'fd_hsl.yaml'
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->imageManager = new ImageManager(array('driver' => 'gd'));
    }

    public function onVichUploaderPostUpload(Event $event){
        
        // return when enable: false
        if($this->config['enable'] === false)
        {
            return;
        }
        
        
        $object = $event->getObject();
        $mapping = $event->getMapping();

        /**
         * @var File $image
         */
        $image = $object->getImageFile();
        
        list($oriWidth, $oriHeight) = getimagesize($image->getPathname());
        $path = $image->getPath();
        $filename = pathinfo($image->getFileName(), PATHINFO_FILENAME);
        $saveFilePath = $image->getPathname(); 

        $imageManager = $this->imageManager->make($image->getPathname());

        // Resize image if enable: true
        if($this->config['resize']['enable'] === true)
        {
            if($oriWidth <= $this->config['resize']['resize_when_width_exceed'])
            {
                return; // do nothing
            }

            $height = round( ($this->config['resize']['resize_to_width'] * $oriHeight) / $oriWidth);
            $imageManager = $imageManager->resize($this->config['resize']['resize_to_width'], $height);
        }

        // Make sure you save this file as image extension, otherwise the file will break.
        if($this->config['save_as_extension'] !== null)
        {
            $saveFilePath = $path.'/'.$filename. '.' . $this->config['save_as_extension'];
            $newFilename = $filename. '.' .$this->config['save_as_extension'];

            /**
             * 1. Delete original uploaded file because it won't replace the original image with
             *    different extension filename.
             * 
             * 2. MUST! Set entity ImageName to new filename.
             */
            unlink($image->getPathname());
            $object->setImageName($newFilename);
        }
        
        $imageManager->save($saveFilePath, $this->config['quality']);
    }
}