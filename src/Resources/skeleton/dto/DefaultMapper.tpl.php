<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $dto_full_class_name ?>;
use <?= $entity_full_class_name ?>;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

class <?= $class_name ?> implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(<?= $dto_class_name ?>::class, <?= $entity_class_name ?>::class);
    }
}
