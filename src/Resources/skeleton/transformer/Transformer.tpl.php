<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use League\Fractal\TransformerAbstract;
<?php if(!$no_entity): ?>use <?= $entity_full_class_name ?>;<?php endif ?>
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
    public function transform(<?php if(!$no_entity):?> ?<?= $entity_class_name ?> $<?= $entity_variable_name ?><?php else:?>$variable<?php endif ?>): ?array
    {
        // Decorate your return data in array form.
<?php if(!$no_entity): ?>
        return $<?= $entity_variable_name ?> ? [
            'id' => $<?= $entity_variable_name ?>->getId(),
        ] : null;
<?php else: ?>
        return $variable ? [] : null; 
<?php endif ?>
    }

    /**
     * Write this function if you have declare something in $availableIncludes or $defaultIncludes
     *
     * Example: If you include 'user', the method name and its parameter will be 'public function includeUser()'
     */
//   public function includeExample(?Entity $entity)
//   {
//       return $entity->getterMethod() ? new Item($entity->getterMethod(), /** transformer class */) : $this->null();
//       return $entity->getterMethod() ? new Collection($entity->getterMethod(), /** transformer class */) : $this->null();
//   }

}