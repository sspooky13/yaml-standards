<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use Symfony\Component\Console\Input\InputInterface;

class InputSettingData
{
    /**
     * @var string
     */
    private $pathToConfigFile;

    /**
     * @var bool
     */
    private $fixEnabled;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->pathToConfigFile = $input->getArgument(YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE);
        $this->fixEnabled = $input->getOption(YamlCommand::OPTION_FIX);
    }

    /**
     * @return string
     */
    public function getPathToConfigFile(): string
    {
        return $this->pathToConfigFile;
    }

    /**
     * @return bool
     */
    public function isFixEnabled(): bool
    {
        return $this->fixEnabled;
    }
}
