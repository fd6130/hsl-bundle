<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fd\HslBundle\Maker;

use App\FractalTrait;
use League\Fractal\TransformerAbstract;
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

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslTransformer extends AbstractMaker
{
    private $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public static function getCommandName(): string
    {
        return 'make:hsl:transformer';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new transformer class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create Transformer (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeHslTransformer.txt'))
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

        $transformerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getShortName(),
            'Transformer\\',
            'Transformer'
        );

        $generator->generateClass(
            $transformerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/transformer/Transformer.tpl.php',
            [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_variable_name' => Str::asLowerCamelCase($entityClassDetails->getShortName()),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new transformer class and start customizing it.',
            'Find the documentation at <fg=yellow>https://github.com/samjarrett/FractalBundle</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(TransformerAbstract::class,'samj/fractal-bundle');
    }
}