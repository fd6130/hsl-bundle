# Transformer

You can customize your result using transformer class.


## Usage

To create a new transformer class, execute the command `php bin/console make:hsl:transformer`.

**That command require you to have an existing entity.**

Or you can create a transformer class without entity `php bin/console make:hsl:transformer --no-entity`.

**Command will generate these for you, you don't have to copy paste from here.**

```
namespace App\Transformer;

use League\Fractal\TransformerAbstract;

/**
 * Transformer are use to decorate your custom output data before serialize it to JSON.
 *
 * Fractal docs: https://fractal.thephpleague.com/transformers
 * Bundle docs: https://github.com/samjarrett/FractalBundle
 */
class ExampleTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */
    public function transform(?$example)
    {
        // Decorate your return data in array form.
        return $example ? [
            'id' => $example->getId()
        ] : null;
    }

    /**
     * Write this function if you have declare something in $availableIncludes or $defaultIncludes
     *
     * Example: If you include 'user', the method name and its parameter will be 'public function includeUser(User $user)'
     */
//   public function includeExample(/** entity class */)
//   {
//       return $this->item(/** entity class */, /** transformer class */);
//   }

}
```

### Use in controller

```
use App\Transformer\ExampleTransformer;
use Fd\HslBundle\Fractal\FractalTrait;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

// your controller class

use FractalTrait;

public function __construct(Manager $manager)
{
    $this->fractal = $manager;
}

public function index()
{
    $entityManager = $this->getDoctrine()->getManager();

    $repository = $entityManager->getRepository(SomeClass::class);
    
    // As a collection
    $data = new Collection($repository->findAll(), ExampleTransformer::class);

    // Or as an item
    // $data = new Item($repository->findAll(), ExampleTransformer::class);

    return $this->json($this->fractal()->createData($data)->toArray());
}


```