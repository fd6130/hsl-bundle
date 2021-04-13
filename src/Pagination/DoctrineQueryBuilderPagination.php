<?php

namespace Fd\HslBundle\Pagination;

use Doctrine\ORM\QueryBuilder;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DoctrineQueryBuilderPagination
{
    const MAX_ITEM_PER_PAGE = 20;

    private $router;
    private $requestStack;
    private $maxItemPerPage = self::MAX_ITEM_PER_PAGE;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * Create a paginated collection.
     * Please make sure your transformer services is public. Otherwise the container
     * cannot fetch it.
     * 
     * @param QueryBuilder $qb query builder
     * @param string $serviceId service id
     */
    public function paginate(QueryBuilder $qb, string $serviceId)
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('page', 1);
        $maxPerPage = $request->query->getInt('limit', $this->maxItemPerPage);

        // if ($request->query->has('include')) {
        //     $this->fractalManager->parseIncludes($request->query->get('include'));
        // }

        $adapter = new QueryAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($maxPerPage);
        $pagerfanta->setCurrentPage($page);

        $paginatorAdapter = new PagerfantaPaginatorAdapter($pagerfanta, function(int $page) use ($request, $maxPerPage) {
            $route = $request->attributes->get('_route');
            $inputParams = $request->attributes->get('_route_params');
            $newParams = array_merge($inputParams, $request->query->all());
            $newParams['page'] = $page;
            $newParams['limit'] = $maxPerPage;
            return $this->router->generate($route, $newParams, UrlGeneratorInterface::ABSOLUTE_URL);
        });

        $resource = new Collection($pagerfanta->getCurrentPageResults(), $serviceId);
        $resource->setPaginator($paginatorAdapter);
        
        return $resource;
    }

    /**
     * Set maximum item per page
     */
    public function setMaxItemPerPage(int $max): self
    {
        $this->maxItemPerPage = $max;

        return $this;
    }
}