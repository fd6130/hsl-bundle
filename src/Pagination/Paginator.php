<?php

namespace Fd\HslBundle\Pagination;

use Doctrine\ORM\QueryBuilder;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceAbstract;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Paginator implements PaginatorInterface
{
    private $router;
    private $requestStack;
    private $limit;

    public function __construct(RouterInterface $router, RequestStack $requestStack, int $limit)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($result, $transformer): ResourceAbstract
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request->query->getInt('page', 1);
        $limitPerPage = $request->query->getInt('limit', $this->limit);

        if($result instanceof QueryBuilder)
        {
            $adapter = new QueryAdapter($result);
        }
        else
        {
            $adapter = new ArrayAdapter($result);
        }

        
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($limitPerPage);
        $pagerfanta->setCurrentPage($page);

        $paginatorAdapter = new PagerfantaPaginatorAdapter($pagerfanta, function(int $page) use ($request, $limitPerPage) {
            $route = $request->attributes->get('_route');
            $inputParams = $request->attributes->get('_route_params');
            $newParams = array_merge($inputParams, $request->query->all());
            $newParams['page'] = $page;
            $newParams['limit'] = $limitPerPage;
            return $this->router->generate($route, $newParams, UrlGeneratorInterface::ABSOLUTE_URL);
        });

        $resource = new Collection($pagerfanta->getCurrentPageResults(), $transformer, 'data');
        $resource->setPaginator($paginatorAdapter);
        
        return $resource;
    }

    /**
     * Set maximum item per page
     */
    public function setLimitPerPage(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}