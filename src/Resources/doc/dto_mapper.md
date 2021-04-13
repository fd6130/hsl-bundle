# Dto Mapper

We use dto to handle/validate user input value and use Mapper to map the dto into entity.

## Usage

To create a new dto class, execute the command `php bin/console make:hsl:dto`.

**mapper require you to have an existing entity.**

**Command will generate these for you, you don't have to copy paste from here.**

Dto class:

```
namespace App\Dto\Input;

use Fd\HslBundle\DtoRequestInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ExampleInput implements DtoRequestInterface
{
    /**
     * If you need validation, use @Assert.
     *
     * @Assert\NotBlank(message="name cannot be blank.")
     */
    public $name;

    public function __construct(Request $request)
    {
        $this->name = $request->get('name'); 
        
    }
}
```

Mapper class:

```
namespace App\Dto\Mapper;

use App\Dto\Input\ExampleInput;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\CustomMapper\CustomMapper;
use Doctrine\ORM\EntityManagerInterface;

class ExampleMapperConfig extends CustomMapper implements AutoMapperConfiguratorInterface
{
    /**
     * For use in across methods
     * 
     * @var ExampleInput
     */
    private $source;

    /**
     * For use in across methods
     * 
     * @var your entity class
     */
    private $destination;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(ExampleInput::class, <Your Entity Class>::class)
            ->useCustomMapper($this);
    }

    /**
     * @param ExampleInput $source
     * @param your entity class $destination
     */
    public function mapToObject($source, $destination)
    {
        // uncomment these if you want to use these variable at other method within this class.
        // $this->source = $source;
        // $this->destination = $destination;

        // do the mapping from source to destination
        $destination->setName($source->name);

        return $destination;
    }
}

```

### Use in controller

```
use AutoMapperPlus\AutoMapperInterface;
use Fd\HslBundle\Fractal\FractalTrait;

// your controller class

public function create(ExampleInput $dto, AutoMapperInterface $mapper)
{
    $newEntity = $mapper->map($dto, <Your Entity Class>::class)

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($newEntity);
    $entityManager->flush();

    return $this->json("Create success");
}

public function update(ExampleInput $dto, AutoMapperInterface $mapper)
{
    // You can fetch entity from database and map from dto.
    // For example: $repository->find()
    $mapper->mapToObject($dto, <Your Entity From Database>);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();
    
    return $this->json("Update success");
}


```