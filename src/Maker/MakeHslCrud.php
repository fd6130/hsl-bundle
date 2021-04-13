<?php

namespace Fd\HslBundle\Maker;

use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\InflectorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslCrud extends AbstractMaker
{
    use MakerTrait;

    private $doctrineHelper;

    private $inflector;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;

        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }
    }

    public static function getCommandName(): string
    {
        return 'make:hsl:crud';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates API CRUD for Doctrine entity class')
            ->addArgument('name', InputArgument::OPTIONAL, sprintf('The class name for new CRUD controller (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addArgument('entity-name', InputArgument::OPTIONAL, 'The existing entity class for this CRUD')
            ->setHelp(file_get_contents(__DIR__ . '/../Resources/help/MakeHslCrud.txt'));

        $inputConfig->setArgumentAsNonInteractive('entity-name');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        $argument = $command->getDefinition()->getArgument('entity-name');
        $entityClassname = $io->askQuestion($this->createEntityClassQuestion($argument->getDescription()));
        $input->setArgument('entity-name', $entityClassname);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-name'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];
        $repositoryClassDetails = $generator->createClassNameDetails(
            '\\' . $entityDoctrineDetails->getRepositoryClass(),
            'Repository\\',
            'Repository'
        );

        $repositoryVars = [
            'repository_full_class_name' => $repositoryClassDetails->getFullName(),
            'repository_class_name' => $repositoryClassDetails->getShortName(),
            'repository_var' => lcfirst($this->singularize($repositoryClassDetails->getShortName())),
        ];
        
        // check if got DTO and get it
        $dtoClassname = Str::asClassName($io->ask('Enter dto class name for this CRUD', null, [Validator::class, 'notBlank']));
        $dtoClassDetails = $generator->createClassNameDetails(
            $dtoClassname,
            'Dto\\Input\\'
        );

        if (!class_exists($dtoClassDetails->getFullName())) {
            throw new RuntimeCommandException(sprintf("Class \"%s\" does not exist.", $dtoClassDetails->getFullName()));
        }
        
        // check if got Transformer and get it
        $transformerClassname = Str::asClassName($io->ask('Enter transformer class name for this CRUD', null, [Validator::class, 'notBlank']));
        $transformerClassDetails = $generator->createClassNameDetails(
            $transformerClassname,
            'Transformer\\'
        );

        if (!class_exists($transformerClassDetails->getFullName())) {
            throw new RuntimeCommandException(sprintf("Class \"%s\" does not exist.", $transformerClassDetails->getFullName()));
        }

        $controllerClassDetails = $generator->createClassNameDetails(
            Str::asClassName($input->getArgument('name')) . 'Controller',
            'Controller\\',
            'Controller'
        );


        $entityVarPlural = lcfirst($this->pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst($this->singularize($entityClassDetails->getShortName()));

        $routePath = $this->pluralize(Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()));
        $routeName = Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());

        $generator->generateController(
            $controllerClassDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/crud/Controller.tpl.php',
            array_merge(
                [
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    'dto_full_class_name' => $dtoClassDetails->getFullName(),
                    'dto_class_name' => $dtoClassDetails->getShortName(),
                    'transformer_full_class_name' => $transformerClassDetails->getFullName(),
                    'transformer_class_name' => $transformerClassDetails->getShortName(),
                    'route_path' => $routePath,
                    'route_name' => $routeName,
                    'entity_var_plural' => $entityVarPlural,
                    'entity_var_singular' => $entityVarSingular,
                    'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                ],
                $repositoryVars
            )
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Modify your new CRUD class <fg=yellow>%s</>', $controllerClassDetails->getFullName()));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

    private function pluralize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->pluralize($word);
        }

        return LegacyInflector::pluralize($word);
    }

    private function singularize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->singularize($word);
        }

        return LegacyInflector::singularize($word);
    }
}
