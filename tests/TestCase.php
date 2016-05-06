<?php

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Gummibeer\Backuplay\BackuplayServiceProvider;
use Illuminate\Console\Command;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BackuplayServiceProvider::class,
        ];
    }

    /**
     * Get application timezone.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string
     */
    protected function getApplicationTimezone($app)
    {
        return 'UTC';
    }

    protected function runCommand(Command $command, array $input, OutputInterface $output)
    {
        return $command->run(new ArrayInput($input), $output);
    }
}
