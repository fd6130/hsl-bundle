<?php

namespace Fd\HslBundle\Event\Listener;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Event\Event;

/**
 * Custom listener that modify an image after post.
 */
class VichImageUploadListener
{
    const MAX_WIDTH = 800;
    const MAX_BYTE = 1000000; // equal to 1MB

    /**
     * @var ImageManager $imageManager
     */
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(array('driver' => 'gd'));
    }

    public function onVichUploaderPostUpload(Event $event){
        $object = $event->getObject();
        $mapping = $event->getMapping();

        /**
         * @var File $image
         */
        $image = $object->getImageFile();
        
        if($this->isNotEligibleForResave($image))
        {
            return;
        }
        
        list($oriWidth, $oriHeight) = getimagesize($image->getPathname());
        $path = $image->getPath();
        $filename = pathinfo($image->getFileName(), PATHINFO_FILENAME);
        $saveFilePath = $image->getPathname(); 

        $imageManager = $this->imageManager->make($image->getPathname());

        if($this->isExceedMaxWidth($oriWidth))
        {
            $height = round( (self::MAX_WIDTH * $oriHeight) / $oriWidth);
            $imageManager = $imageManager->resize(self::MAX_WIDTH, $height);
        }

        
        if($this->notJPGExtension($image))
        {
            $saveFilePath = $path.'/'.$filename. '.jpg';
            $newFilename = $filename. '.jpg';

            /**
             * 1. Delete original uploaded file because it won't replace the original image with
             *    different extension filename.
             * 
             * 2. MUST! Set entity ImageName to new filename.
             */
            unlink($image->getPathname());
            $object->setImageName($newFilename);
        }
        
        $imageManager->save($saveFilePath, 90);
    }

    private function notJPGExtension($image)
    {
        return $image->getExtension() !== 'jpg';
    }

    private function isExceedMaxWidth($imageWidth)
    {
        return $imageWidth > self::MAX_WIDTH;
    }

    private function isNotEligibleForResave(File $image)
    {
        return filesize($image->getPathname()) < self::MAX_BYTE && !$this->notJPGExtension($image);
    }
}