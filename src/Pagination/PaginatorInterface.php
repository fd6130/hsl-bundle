<?php

namespace Fd\HslBundle\Pagination;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\Resource\ResourceAbstract;

interface PaginatorInterface
{
    const DEFAULT_LIMIT_VALUE = 30;
    
    /**
     * Paginate the result and transform the value using transformer.
     * 
     * @param Query|QueryBuilder|array|Collection $result Result to be paginate
     * @param string $transformer Service id or FQCN
     * @param int|null $lifetime For doctrine cache only - cache lifetime
     * @param string|null $resultCacheId For doctrine cache only - unique cache id
     * 
     * @return ResourceAbstract
     */
    public function paginate($result, $transformer, ?int $lifetime = null, ?string $resultCacheId = null): ResourceAbstract;

    public function setLimitPerPage(int $max);
}