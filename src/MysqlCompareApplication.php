<?php namespace LLoadout\MysqlCompare;

use Symfony\Component\Console\Output\ConsoleOutput;

class MysqlCompareApplication extends \Symfony\Component\Console\Application
{
    public function __construct(string $name, string $version, $args)
    {
        $output = new ConsoleOutput();

        parent::__construct($name, $version);
    }


}
