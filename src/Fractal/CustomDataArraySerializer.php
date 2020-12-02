<?php

namespace Fd\HslBundle\Fractal;

use League\Fractal\Serializer\DataArraySerializer;

/**
 * To remove "data" on item and null resource.
 */
class CustomDataArraySerializer extends DataArraySerializer
{
    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        return $data;
    }

    /**
     * Serialize null resource.
     *
     * @return array
     */
    public function null()
    {
        return [];
    }
}