<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use League\Fractal\TransformerAbstract;
use <?= $entity_full_class_name ?>;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

/**
 * Transformer are use to decorate your custom output data before serialize it to JSON.
 *
 * Fractal docs: https://fractal.thephpleague.com/transformers
 * Bundle docs: https://github.com/samjarrett/FractalBundle
 */
class <?= $class_name ?> extends TransformerAbstract
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
    public function transform(?<?= $entity_class_name ?> $<?= $entity_variable_name ?>): ?array
    {
        // Decorate your return data in array form.
        return $<?= $entity_variable_name ?> ? [
            'id' => $<?= $entity_variable_name ?>->getId(),
        ] : null;
    }

    /**
     * Write this function if you have declare something in $availableIncludes or $defaultIncludes
     *
     * Example: If you include 'user', the method name and its parameter will be 'public function includeUser()'
     */
//   public function includeExample(?<?= $entity_class_name ?> $<?= $entity_variable_name ?>)
//   {
//       return $<?= $entity_variable_name ?>->getterMethod() ? $this->item($<?= $entity_variable_name ?>->getterMethod(), /** transformer class */) : $this->null();
//   }

}