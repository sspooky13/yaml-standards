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
     * @var string
     */
    private $pathToCacheDir;

    /**
     * @var bool
     */
    private $disableCache;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        /** @var string $pathToConfigFile */
        $pathToConfigFile = $input->getArgument(YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE);
        $this->pathToConfigFile = $pathToConfigFile;
        $this->fixEnabled = $input->getOption(YamlCommand::OPTION_FIX);
        $this->pathToCacheDir = $input->getOption(YamlCommand::OPTION_PATH_TO_CACHE_DIR);
        $this->disableCache = $input->getOption(YamlCommand::OPTION_DISABLE_CACHE);
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

    /**
     * @return string
     */
    public function getPathToCacheDir(): string
    {
        return $this->pathToCacheDir;
    }

    /**
     * @return bool
     */
    public function isCacheDisabled(): bool
    {
        return $this->disableCache;
    }
}
