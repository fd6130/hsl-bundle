<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use AutoMapperPlus\AutoMapperInterface;
use <?= $dto_full_class_name ?>;
use <?= $entity_full_class_name ?>;
use <?= $transformer_full_class_name ?>;
use <?= $repository_full_class_name ?>;
use Fd\HslBundle\Fractal\FractalTrait;
use Fd\HslBundle\Pagination\PaginatorInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use MonterHealth\ApiFilterBundle\MonterHealthApiFilter;
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("<?= $route_path ?>")
 */
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
    use FractalTrait;

    private $mapper;

    public function __construct(Manager $manager, AutoMapperInterface $mapper)
    {
        $this->fractal = $manager;
        $this->mapper = $mapper;
    }

    /**
     * @Route("", name="<?= $route_name ?>_collection", methods={"GET"})
     */
    public function collection(Request $request, <?= $repository_class_name ?> $<?= $repository_var ?>, PaginatorInterface $pagination, MonterHealthApiFilter $monterHealthApiFilter): Response
    {
        $qb = $<?= $repository_var ?>->createQueryBuilder('qb');
        $monterHealthApiFilter->addFilterConstraints($qb, $<?= $repository_var ?>->getClassName(), $request->query);

        $data = $pagination->paginate($qb, <?= $transformer_class_name ?>::class);
        
        return $this->json($this->fractal($request)->createData($data)->toArray());
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_item", methods={"GET"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
    public function item(<?= $entity_class_name ?> $<?= $entity_var_singular ?>, Request $request): Response
    {
        $data = new Item($<?= $entity_var_singular ?>, <?= $transformer_class_name ?>::class);

        return $this->json($this->fractal($request)->createData($data)->toArray());
    }

    /**
     * @Route("", name="<?= $route_name ?>_create", methods={"POST"})
     */
    public function create(<?= $dto_class_name ?> $dto)
    {
        $<?= $entity_var_singular ?> = $this->mapper->map($dto, <?= $entity_class_name ?>::class);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($<?= $entity_var_singular ?>);
        $entityManager->flush();

        return $this->json(['id' => $<?= $entity_var_singular ?>->getId()], 201);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_update", methods={"PUT"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
    public function update(<?= $entity_class_name ?> $<?= $entity_var_singular ?>, <?= $dto_class_name ?> $dto): Response
    {
        $this->mapper->mapToObject($dto, $<?= $entity_var_singular ?>);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response('', 204);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_delete", methods={"DELETE"}, requirements={"<?= $entity_identifier ?>"="\d+"})
     */
    public function delete(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($<?= $entity_var_singular ?>);
        $entityManager->flush();

        return new Response('', 204);
    }
}
