<?php

namespace Fd\HslBundle\Fractal;

use League\Fractal\Manager;
use Symfony\Component\HttpFoundation\Request;

trait FractalTrait
{

    /**
     * Recommend you to use factal() instead of this one.
     * 
     * @var Manager
     */
    protected $fractal;

    /**
     * Get fractal manager w/o includes.
     * 
     * At here i will set to my CustomDataArraySerializer instead of default one.
     * 
     * @return Manager
     */
    protected function fractal(Request $request = null)
    {
        if($request)
        {
            if ($request->query->has('include')) {
                $this->fractal->parseIncludes($request->query->get('include'));
            }
        }
        
        return $this->fractal->setSerializer(new CustomDataArraySerializer());
    }
}