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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

/**
 * @author fd6130 <https://github.com/fd6130>
 */
final class MakeHslTransformer extends AbstractMaker
{
    use MakerTrait;
    
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
            ->addArgument('class-name', InputArgument::OPTIONAL, sprintf('The class name of new Transformer (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('no-entity', null, InputOption::VALUE_NONE, 'Use this option to generate a plank transformer')
            ->setHelp(file_get_contents(__DIR__ . '/../Resources/help/MakeHslTransformer.txt'));

        $inputConf->setArgumentAsNonInteractive('entity-class');
        $inputConf->setArgumentAsNonInteractive('class-name');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if ($input->getOption('no-entity')) {
            $io->block([
                'Note: You have choose to generate a plank Transformer.',
            ], null, 'fg=yellow');
            $classname = $io->ask(sprintf('The class name of new Transformer (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())), null, [Validator::class, 'notBlank']);
            
            $input->setArgument('class-name', $classname);
        }
        else
        {
            $argument = $command->getDefinition()->getArgument('entity-class');
            $question = $this->createEntityClassQuestion($argument->getDescription());
            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $noEntity = $input->getOption('no-entity');
        $classname = !$noEntity ? null : Str::asClassName($input->getArgument('class-name'));

        $entityClassDetails = !$noEntity ? $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        ) : null;

        $transformerClassDetails = !$noEntity ? $generator->createClassNameDetails(
            $entityClassDetails->getShortName(),
            'Transformer\\',
            'Transformer'
        ) : $generator->createClassNameDetails($classname, 'Transformer\\', 'Transformer');

        $generator->generateClass(
            $transformerClassDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/transformer/Transformer.tpl.php',
            [
                'no_entity' => $noEntity,
                'entity_class_name' => $entityClassDetails ? $entityClassDetails->getShortName() : null,
                'entity_variable_name' => $entityClassDetails ? Str::asLowerCamelCase($entityClassDetails->getShortName()) : null,
                'entity_full_class_name' => $entityClassDetails ? $entityClassDetails->getFullName(): null,
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
        $dependencies->addClassDependency(TransformerAbstract::class, 'samj/fractal-bundle');
    }

    private function createEntityClassQuestion(string $questionText): Question
    {
        $question = new Question($questionText);
        $question->setValidator([Validator::class, 'notBlank']);
        $question->setAutocompleterValues($this->doctrineHelper->getEntitiesForAutocomplete());

        return $question;
    }
}
