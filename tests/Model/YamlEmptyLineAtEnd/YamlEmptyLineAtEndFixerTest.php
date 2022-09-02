<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

/**
 * Fix yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndFixerTest extends TestCase
{
    public function testFixerFixFilesCorrect(): void
    {
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/version.yaml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
            __DIR__ . '/resource/unfixed/foo.js',
            __DIR__ . '/resource/unfixed/foo.json',
            __DIR__ . '/resource/unfixed/foo.less',
            __DIR__ . '/resource/unfixed/foo.md',
            __DIR__ . '/resource/unfixed/foo.php',
            __DIR__ . '/resource/unfixed/foo.xml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/version.yaml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
            __DIR__ . '/resource/fixed/foo.js',
            __DIR__ . '/resource/fixed/foo.json',
            __DIR__ . '/resource/fixed/foo.less',
            __DIR__ . '/resource/fixed/foo.md',
            __DIR__ . '/resource/fixed/foo.php',
            __DIR__ . '/resource/fixed/foo.xml',
        ];

        $yamlEmptyLineAtEndFixer = new YamlEmptyLineAtEndFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $tempCorrectFile = $this->getTempCorrectFile($pathToUnfixedFile);
            $standardParametersData = $this->getStandardsParametersData();
            $yamlEmptyLineAtEndFixer->fix($pathToUnfixedFile, $tempCorrectFile, $standardParametersData);
            $yamlFileContent = file_get_contents($tempCorrectFile);
            $correctYamlFileContent = file_get_contents($pathToFixedFiles[$key]);

            $this->assertSame($correctYamlFileContent, $yamlFileContent);
        }
    }

    /**
     * @param string $pathToUnfixedFile
     * @return string
     */
    private function getTempCorrectFile(string $pathToUnfixedFile): string
    {
        $extension = pathinfo($pathToUnfixedFile)['extension'];

        return __DIR__ . '/resource/temp/noName.' . $extension;
    }

    /**
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(): StandardParametersData
    {
        return new StandardParametersData(4, 4, 2, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false);
    }
}
