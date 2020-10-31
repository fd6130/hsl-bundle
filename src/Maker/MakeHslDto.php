<?php

namespace Fd\HslBundle\Maker;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use League\Fractal\TransformerAbstract;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\EventRegistry;
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
use SymfonyBundles\JsonRequestBundle\SymfonyBundlesJsonRequestBundle;

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslDto extends AbstractMaker
{

    public static function getCommandName(): string
    {
        return 'make:hsl:dto';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new dto input class')
            ->addArgument('dto_name', InputArgument::OPTIONAL, 'Choose a class name for your dto (e.g. <fg=yellow>User</>)')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeHslDto.txt'))
        ;

        //$inputConf->setArgumentAsNonInteractive('event');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $dtoClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('dto_name'),
            'Dto\\Input\\',
            'Input'
        );

        $this->generateDto($input, $io, $generator, $dtoClassNameDetails);
        $this->generateCustomMapper($input, $io, $generator, $dtoClassNameDetails);

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new dto input class and start customizing it.',
            'Find the documentation at <fg=yellow>https://github.com/mark-gerarts/automapper-plus-bundle</>',
        ]);
    }

    public function generateDto(InputInterface $input, ConsoleStyle $io, Generator $generator, $dtoClassNameDetails)
    {
        $fieldArray = [];
        $addNewProperty = "first_property";

        while(true)
        {
            $addNewProperty = $io->ask('New property name (press <return> to stop adding fields)');
            if(empty($addNewProperty))
            {
                break;
            }
            $fieldName = Str::asLowerCamelCase($addNewProperty);
            if(in_array($fieldName, $fieldArray))
            {
                $io->error(sprintf("The \"%s\" property already exists.", $fieldName));
                continue;
                
            }

            $requestType = $io->ask('Is this JSON or Form request? (json/form)', 'json', function($value) {

                if(strtolower($value) !== 'json' && strtolower($value) !== 'form')
                {
                    throw new RuntimeCommandException('Value must be \'json\' or \'form\'');
                }

                return $value;
            });

            $fieldArray [] = [
                'name' => $fieldName,
                'type' => $requestType
            ];
        }

        // generate dto
        $generator->generateClass(
            $dtoClassNameDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/dto/Dto.tpl.php',
            [
                'fieldArray' => $fieldArray
            ]
        );
    }

    public function generateCustomMapper(InputInterface $input, ConsoleStyle $io, Generator $generator, $dtoClassNameDetails)
    {
        $customMapper = $io->confirm('Do you need custom mapper configuration?');

        if($customMapper)
        {
            $mapperClassNameDetails = $generator->createClassNameDetails(
                $input->getArgument('dto_name'),
                'Dto\\Mapper\\',
                'MapperConfig'
            );

            $entityName = $io->ask('Which entity you gonna map with?', null, function($answer) {
                if(empty($answer))
                {
                    throw new RuntimeCommandException('This value cannot be blank.');
                }
                
                return $answer;
            });
            
            $entityClassNameDetails = $generator->createClassNameDetails(
                $entityName,
                'Entity\\'
            );

            $entityClassExists = class_exists($entityClassNameDetails->getFullName());

            while(!$entityClassExists)
            {
                $io->error(sprintf('Could not find entity \'%s\'', $entityClassNameDetails->getFullName()));
                $entityClass = $io->ask('Which entity you gonna use for this transformer?');
                $entityClassNameDetails = $generator->createClassNameDetails(
                    $entityClass,
                    'Entity\\'
                );

                $entityClassExists = class_exists($entityClassNameDetails->getFullName());
            }

            // generate mapper config
            $generator->generateClass(
                $mapperClassNameDetails->getFullName(),
                __DIR__.'/../Resources/skeleton/dto/Mapper.tpl.php',
                [
                    'dto_short_class_name' => $dtoClassNameDetails->getShortName(),
                    'dto_full_class_name' => $dtoClassNameDetails->getFullName(),
                    'entity_short_class_name' => $entityClassNameDetails->getShortName(),
                    'entity_full_class_name' => $entityClassNameDetails->getFullName(),
                ]
            );
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(SymfonyBundlesJsonRequestBundle::class,'symfony-bundles/json-request-bundle');
        $dependencies->addClassDependency(AutoMapperConfiguratorInterface::class, 'mark-gerarts/automapper-plus-bundle');
    }
}