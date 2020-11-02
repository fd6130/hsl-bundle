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
use Symfony\Component\Console\Question\Question;
use SymfonyBundles\JsonRequestBundle\SymfonyBundlesJsonRequestBundle;

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslDto extends AbstractMaker
{
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
            ->setDescription('Creates a new dto input class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create DTO (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeHslDto.txt'))
        ;

        //$inputConf->setArgumentAsNonInteractive('event');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $dtoClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getShortName(),
            'Dto\\Input\\',
            'Input'
        );

        $fieldArray = [];
        $addNewProperty = "first_property";

        do
        {
            $addNewProperty = $io->ask('New property name (press <return> to stop adding fields)', null, function($answer) {
                if(empty($answer))
                {
                    throw new RuntimeCommandException('This value cannot be blank.');
                }

                return $answer;
            });

            $fieldName = Str::asLowerCamelCase($addNewProperty);

            if(in_array($fieldName, $fieldArray))
            {
                $io->error(sprintf("The \"%s\" property already exists.", $fieldName));
                continue;
                
            }

            $requestType = $io->ask('Is this JSON or Form request? (json/form/file)', 'json', function($value) {

                if(strtolower($value) !== 'json' && strtolower($value) !== 'form' && strtolower($value) !== 'file')
                {
                    throw new RuntimeCommandException("Value must be 'json', 'form' or 'file'");
                }

                return $value;
            });

            $fieldArray [] = [
                'name' => $fieldName,
                'type' => $requestType
            ];
        }while(empty($addNewProperty));

        // generate dto
        $generator->generateClass(
            $dtoClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/dto/Dto.tpl.php',
            [
                'fieldArray' => $fieldArray
            ]
        );

        // ask if need custom mapper
        $customMapper = $io->confirm('Do you need custom mapper configuration?');
        
        if($customMapper)
        {
            $mapperClassNameDetails = $generator->createClassNameDetails(
                $entityClassDetails->getShortName(),
                'Dto\\Mapper\\',
                'MapperConfig'
            );

            // generate mapper config
            $generator->generateClass(
                $mapperClassNameDetails->getFullName(),
                __DIR__.'/../Resources/skeleton/dto/Mapper.tpl.php',
                [
                    'dto_class_name' => $dtoClassDetails->getShortName(),
                    'dto_full_class_name' => $dtoClassDetails->getFullName(),
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                ]
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new dto input class and start customizing it.',
            'Find the documentation at <fg=yellow>https://github.com/mark-gerarts/automapper-plus-bundle</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(SymfonyBundlesJsonRequestBundle::class,'symfony-bundles/json-request-bundle');
        $dependencies->addClassDependency(AutoMapperConfiguratorInterface::class, 'mark-gerarts/automapper-plus-bundle');
    }
}