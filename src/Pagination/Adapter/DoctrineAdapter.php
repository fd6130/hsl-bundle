<?php declare(strict_types=1);

namespace Fd\HslBundle\Pagination\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Adapter which calculates pagination from a Doctrine ORM Query or QueryBuilder.
 */
class DoctrineAdapter implements AdapterInterface
{
    private Paginator $paginator;
    private ?int $lifetime;
    private ?string $resultCacheId;

    /**
     * @param Query|QueryBuilder $query
     * @param bool               $fetchJoinCollection Whether the query joins a collection (true by default)
     * @param bool|null          $useOutputWalkers    Flag indicating whether output walkers are used in the paginator
     */
    public function __construct($query, bool $fetchJoinCollection = true, ?bool $useOutputWalkers = null, ?int $lifetime = null, ?string $resultCacheId = null)
    {
        $this->paginator = new Paginator($query, $fetchJoinCollection);
        $this->paginator->setUseOutputWalkers($useOutputWalkers);
        $this->lifetime = $lifetime;
        $this->resultCacheId = $resultCacheId;
    }

    public function getQuery(): Query
    {
        return $this->paginator->getQuery();
    }

    public function getFetchJoinCollection(): bool
    {
        return $this->paginator->getFetchJoinCollection();
    }

    public function getNbResults(): int
    {
        return \count($this->paginator);
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $paginator = $this->paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);
                
        if($this->lifetime == null && $this->resultCacheId == null)
        {
            return $this->paginator->getIterator();
        }
        
        $paginator->enableResultCache($this->lifetime, $this->resultCacheId);

        return $this->paginator->getIterator();
    }
}
