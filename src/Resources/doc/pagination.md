# Pagination

To paginate your result, inject `PaginatorInterface` to your controller and call `paginate()` with query builder/array and transformer class.

```
use Fd\HslBundle\Fractal\FractalTrait;
use Fd\HslBundle\Pagination\PaginatorInterface;
use League\Fractal\Manager;

// your controller class

use FractalTrait;

public function __construct(Manager $manager)
{
    $this->fractal = $manager;
}

public function index(PaginatorInterface $paginator)
{
    $entityManager = $this->getDoctrine()->getManager();

    $repository = $entityManager->getRepository(SomeClass::class);
    
    //You can pass array or query builder to first parameter.
    $data = $paginator->paginate($repository->findAll(), SomeTransformer::class);

    return $this->json($this->fractal()->createData($data)->toArray());
}

```