<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlIndentFixerTest extends TestCase
{
    public function testFixUnfixedFile(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, false);
        $pathToUnfixedFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';
        $pathToFixedFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';

        $this->checkFile($pathToUnfixedFile, $standardParametersData, $pathToFixedFile);
    }

    public function testFixUnfixedFileWithPreservedComments(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_PRESERVED, false);
        $pathToUnfixedFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';
        $pathToFixedFile = __DIR__ . '/resource/fixed/yaml-getting-started-with-preserved-comments.yml';

        $this->checkFile($pathToUnfixedFile, $standardParametersData, $pathToFixedFile);
    }

    public function testFixUnfixedFileWithIgnoredCommentsIndent(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, true);

        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-config.yml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/symfony-service-with-ignored-comments-indent.yml',
            __DIR__ . '/resource/fixed/symfony-security-with-ignored-comments-indent.yml',
            __DIR__ . '/resource/fixed/symfony-config-with-ignored-comments-indent.yml',
        ];

        $this->checkFiles($pathToUnfixedFiles, $standardParametersData, $pathToFixedFiles);
    }

    public function testFixUnfixedFiles(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, false);
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/arraysWithUnquotedColons.yml',
            __DIR__ . '/resource/unfixed/kubernetes-postgres.yml',
            __DIR__ . '/resource/unfixed/kustomization.yaml',
            __DIR__ . '/resource/unfixed/mkdocs.yml',
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
            __DIR__ . '/resource/unfixed/yaml-standards.yaml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/arraysWithUnquotedColons.yml',
            __DIR__ . '/resource/fixed/kubernetes-postgres.yml',
            __DIR__ . '/resource/fixed/kustomization.yaml',
            __DIR__ . '/resource/fixed/mkdocs.yml',
            __DIR__ . '/resource/fixed/shopsys-service.yml',
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
            __DIR__ . '/resource/fixed/yaml-standards.yaml',
        ];

        $this->checkFiles($pathToUnfixedFiles, $standardParametersData, $pathToFixedFiles);
    }

    /**
     * @return string
     */
    private function getTempCorrectYamlFile(): string
    {
        return __DIR__ . '/resource/temp/noName.yml';
    }

    /**
     * @param string $indentsCommentsWithoutParent
     * @param bool $ignoreCommentsIndent
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $indentsCommentsWithoutParent, bool $ignoreCommentsIndent): StandardParametersData
    {
        return new StandardParametersData(4, 4, 4, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, $indentsCommentsWithoutParent, [], $ignoreCommentsIndent);
    }

    /**
     * @param string $pathToUnfixedFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @param string $pathToFixedFile
     */
    protected function checkFile(string $pathToUnfixedFile, StandardParametersData $standardParametersData, string $pathToFixedFile): void
    {
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentFixer = new YamlIndentFixer();
        $yamlIndentFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);

        $yamlFileContent = file_get_contents($tempCorrectYamlFile);
        $correctYamlFileContent = file_get_contents($pathToFixedFile);

        $this->assertSame($correctYamlFileContent, $yamlFileContent);
    }

    /**
     * @param array $pathToUnfixedFiles
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @param array $pathToFixedFiles
     */
    protected function checkFiles(array $pathToUnfixedFiles, StandardParametersData $standardParametersData, array $pathToFixedFiles): void
    {
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentFixer = new YamlIndentFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $yamlIndentFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedFiles[$key]);

            $this->assertSame($correctYamlFileContent, $yamlFileContent);
        }
    }
}
