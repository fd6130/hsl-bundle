<?php

namespace Fd\HslBundle\Maker;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use League\Fractal\TransformerAbstract;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use SymfonyBundles\JsonRequestBundle\JsonRequestBundle;

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslDto extends AbstractMaker
{
    use MakerTrait;

    private $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public static function getCommandName(): string
    {
        return 'make:hsl:dto';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new dto input class for mapping')
            ->addArgument('dto-class-name', InputArgument::OPTIONAL, sprintf('Enter the dto class name (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addArgument('entity-class-name', InputArgument::OPTIONAL, sprintf('Enter the entity class name for mapping (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('generate-mapper', null, InputOption::VALUE_NONE, 'Simply generate a mapper for existing dto')
            ->setHelp(file_get_contents(__DIR__ . '/../Resources/help/MakeHslDto.txt'));

        $inputConf->setArgumentAsNonInteractive('class-name');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if ($input->getOption('generate-mapper'))
        {
            $io->block([
                'Note: You have choose to generate a mapper for existing dto.',
            ], null, 'fg=yellow');
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $generateMapper = $input->getOption('generate-mapper');
        $classname = Str::asClassName($input->getArgument('dto-class-name'));
        $entityClassname = Str::asClassName($input->getArgument('entity-class-name'));

        // make sure entity is exist.
        $generator->createClassNameDetails(
            Validator::entityExists($entityClassname, $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $dtoClassDetails = $generator->createClassNameDetails(
            $classname,
            'Dto\\Input\\',
            'Input'
        );

        if (!$generateMapper && class_exists($dtoClassDetails->getFullName()))
        {
            throw new RuntimeCommandException(sprintf("The class \"%s\" already exists.", $dtoClassDetails->getFullName()));
        }
        
        // if only to generate a mapper, skip the rest of the code after generate.
        if ($generateMapper)
        {
            if (!class_exists($dtoClassDetails->getFullName()))
            {
                throw new RuntimeCommandException(sprintf("You must create a DTO (make:hsl:dto) first."));
            }

            $mapper = $generator->createClassNameDetails(
                $classname,
                'Dto\\Mapper\\',
                'MapperConfig'
            );

            if (class_exists($mapper->getFullName()))
            {
                throw new RuntimeCommandException(sprintf("You already have mapper for this dto."));
            }

            $this->askCreateCustomOrDefaultMapper($classname, $entityClassname, $dtoClassDetails, $io, $generator);
        }
        else
        {
            $this->generateDto($dtoClassDetails, $io, $generator);
            $this->askCreateCustomOrDefaultMapper($classname, $entityClassname, $dtoClassDetails, $io, $generator);
        }

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Go to \'src/Dto\' and start customizing your dto class.',
            'Read the documentation about mapping at <fg=yellow>https://github.com/mark-gerarts/automapper-plus-bundle</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(JsonRequestBundle::class, 'symfony-bundles/json-request-bundle');
        $dependencies->addClassDependency(AutoMapperConfiguratorInterface::class, 'mark-gerarts/automapper-plus-bundle');
    }

    private function generateDto($dtoClassDetails, ConsoleStyle $io, Generator $generator)
    {
        $fieldArray = [];
        $addNewProperty = $io->ask('New property name (press <return> to stop adding fields)');

        while (!empty($addNewProperty))
        {
            $fieldName = Str::asLowerCamelCase($addNewProperty);

            if (in_array($fieldName, $fieldArray))
            {
                $io->error(sprintf("The \"%s\" property already exists.", $fieldName));
                continue;
            }

            $requestType = $io->ask('Is this JSON or Form request? (json/form/file)', 'json', function ($value)
            {

                if (strtolower($value) !== 'json' && strtolower($value) !== 'form' && strtolower($value) !== 'file')
                {
                    throw new RuntimeCommandException("Value must be 'json', 'form' or 'file'");
                }

                return $value;
            });

            $fieldArray[] = [
                'name' => $fieldName,
                'type' => $requestType
            ];

            $addNewProperty = $io->ask('New property name (press <return> to stop adding fields)');
        }

        // generate dto
        $generator->generateClass(
            $dtoClassDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/dto/Dto.tpl.php',
            [
                'fieldArray' => $fieldArray
            ]
        );

        $generator->writeChanges();
    }

    private function askCreateCustomOrDefaultMapper($classname, $entityClassname, $dtoClassDetails, ConsoleStyle $io, Generator $generator)
    {
        $customMapper = $io->confirm('Do you need custom mapper configuration?', false);

        $this->generateMapper($customMapper, $classname, $entityClassname, $dtoClassDetails, $generator);
    }

    private function generateMapper(bool $customMapper, $classname, $entityClassname, $dtoClassDetails, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($entityClassname, $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $mapperClassNameDetails = $generator->createClassNameDetails(
            $classname,
            'Dto\\Mapper\\',
            'MapperConfig'
        );

        // generate mapper config
        $generator->generateClass(
            $mapperClassNameDetails->getFullName(),
            __DIR__ . ($customMapper === true ? '/../Resources/skeleton/dto/CustomMapper.tpl.php' : '/../Resources/skeleton/dto/DefaultMapper.tpl.php'),
            [
                'dto_class_name' => $dtoClassDetails->getShortName(),
                'dto_full_class_name' => $dtoClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
            ]
        );

        $generator->writeChanges();
    }
}
