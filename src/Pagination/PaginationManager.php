<?php

namespace Fd\HslBundle\Pagination;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class PaginationManager implements ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public static function getSubscribedServices()
    {
        return [
            DoctrineQueryBuilderPagination::class
        ];
    }

    public function getTest()
    {
        return "Test";
    }

    public function getQueryBuilderPagination() : DoctrineQueryBuilderPagination
    {
        return $this->locator->get(DoctrineQueryBuilderPagination::class);
    }
}