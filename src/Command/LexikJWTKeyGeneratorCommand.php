<?php

namespace Fd\HslBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Generate SSH keys.
 */
class LexikJWTKeyGeneratorCommand extends Command
{
    protected static $defaultName = 'lexik:generate-keys';

    protected function configure()
    {
        $this->setDescription('Generate SSH keys for LexikJWTAuthenticatorBundle.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>',
            '=========================================================',
            '| SSH keys generator for LexikJWTAuthenticatorBundle    |',
            '|                                                       |',
            "| Generated keys will be save to 'config/jwt' folder.   |",
            '=========================================================',
            '</info>',
            '',
        ]);
        
        $output->writeln('Creating folder \'config/jwt\'...');
        $output->writeln($this->makeDirectory());
        $output->writeln([
            'Done.',
            '',
        ]);

        $output->writeln('Generate Secret key...');
        $output->writeln($this->generatePrivateKey());
        $output->writeln([
            'Done.',
            '',
        ]);

        $output->writeln('Generate Public key...');
        $output->writeln($this->generatePublicKey());
        $output->writeln('Done.');
        $output->writeln([
            '',
            '<info>Remember to double check your \'config/packages/lexik_jwt_authentication.yaml\' and \'.env\' for configuration.</info>',
            ''
        ]);

        return Command::SUCCESS;
    }

    public function makeDirectory()
    {
        $process = new Process(['mkdir', '-p', 'config/jwt']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function generatePrivateKey()
    {
        $process = new Process(['openssl', 'genpkey', '-out', 'config/jwt/private.pem', '-aes256', '-algorithm', 'rsa', '-pkeyopt', 'rsa_keygen_bits:4096']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function generatePublicKey()
    {
        $process = new Process(['openssl', 'pkey', '-in', 'config/jwt/private.pem', '-out', 'config/jwt/public.pem', '-pubout']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}