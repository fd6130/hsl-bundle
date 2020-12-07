<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $dto_full_class_name ?>;
use <?= $entity_full_class_name ?>;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\CustomMapper\CustomMapper;
use Doctrine\ORM\EntityManagerInterface;

class <?= $class_name ?> extends CustomMapper implements AutoMapperConfiguratorInterface
{
    /**
     * For use in across methods
     * 
     * @var <?= $dto_class_name."\n" ?>
     */
    private $source;

    /**
     * For use in across methods
     * 
     * @var <?= $entity_class_name."\n" ?>
     */
    private $destination;

    //private $entityManager;

    //public function __construct(EntityManagerInterface $entityManager)
    //{
    //    $this->entityManager = $entityManager;
    //}

    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(<?= $dto_class_name ?>::class, <?= $entity_class_name ?>::class)
            ->useCustomMapper($this);
    }

    /**
     * @param <?= $dto_class_name ?> $source
     * @param <?= $entity_class_name ?> $destination
     */
    public function mapToObject($source, $destination)
    {
        // uncomment these if you want to use these variable at other method within this class.
        // $this->source = $source;
        // $this->destination = $destination;

        // do the mapping from source to destination

        return $destination;
    }
}
