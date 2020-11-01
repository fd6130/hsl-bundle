<?php

namespace Fd\HslBundle\Pagination;

use League\Fractal\Resource\ResourceAbstract;

interface PaginatorInterface
{
    const DEFAULT_LIMIT_VALUE = 30;
    
    /**
     * Paginate the result and transform the value using transformer.
     * 
     * @param QueryBuilder|array|Collection $result result to be paginate
     * @param string $transformer transformer service id
     * 
     * @return ResourceAbstract
     */
    public function paginate($result, $transformer): ResourceAbstract;

    public function setLimitPerPage(int $max);
}