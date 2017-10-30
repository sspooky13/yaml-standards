<?php

use YamlAlphabeticalChecker\YamlCheckCommand;
use Symfony\Component\Console\Application;

$files = [
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

$autoloadFileFound = false;
foreach ($files as $file) {
    if (file_exists($file)) {
        require $file;
        $autoloadFileFound = true;
        break;
    }
}
if (!$autoloadFileFound) {
    throw new BadFunctionCallException('vendor/autoload.php not found' . PHP_EOL);
}

$application = new Application('YAML alphabetical checker');
$application->setCatchExceptions(false);
$application->add(new YamlCheckCommand());
$application->setDefaultCommand('yaml-alphabetical-check', true);
$application->run();
