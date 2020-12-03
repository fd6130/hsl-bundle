<?php

namespace Fd\HslBundle\Fractal;

use League\Fractal\Serializer\DataArraySerializer;

class CustomDataArraySerializer extends DataArraySerializer
{
    /**
     * @inheritdoc
     */
    public function collection($resourceKey, array $data)
    {
        if($resourceKey)
        {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function item($resourceKey, array $data)
    {
        if($resourceKey)
        {
            return [$resourceKey => $data];
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function null()
    {
        return [];
    }
}