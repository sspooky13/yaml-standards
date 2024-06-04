<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;
use YamlStandards\Result\Result;

class YamlIndentCheckerTest extends TestCase
{
    public function testCheckUnfixedFileIsNotCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT);
        $pathToFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

        $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
    }

    public function testCheckUnfixedFilesIsNotCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT);
        $pathToFiles = [
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
        $yamlIndentChecker = new YamlIndentChecker();

        foreach ($pathToFiles as $pathToFile) {
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);
            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFileIsCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT);
        $pathToFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

        $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
    }

    public function testCheckFixedFileIsCorrectWithPreservedComments(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_PRESERVED);
        $pathToFile = __DIR__ . '/resource/fixed/yaml-getting-started-with-preserved-comments.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

        $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
    }

    public function testCheckFixedFilesIsCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT);
        $pathToFiles = [
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
        $yamlIndentChecker = new YamlIndentChecker();

        foreach ($pathToFiles as $pathToFile) {
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);
            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML indent check of "%s" failed.', $pathToFile));
        }
    }

    /**
     * @param string $indentsCommentsWithoutParent
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $indentsCommentsWithoutParent): StandardParametersData
    {
        return new StandardParametersData(4, 4, 4, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, $indentsCommentsWithoutParent, [], false);
    }
}
