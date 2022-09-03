<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;
use YamlStandards\Result\Result;

class YamlAlphabeticalCheckerTest extends TestCase
{
    public function testCheckFixedFilesIsCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/fixed/arraysWithUnquotedColons.yml',
            __DIR__ . '/resource/fixed/images.yml',
            __DIR__ . '/resource/fixed/kubernetes-postgres.yml',
            __DIR__ . '/resource/fixed/kustomization.yaml',
            __DIR__ . '/resource/fixed/shopsys-service.yml',
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
            __DIR__ . '/resource/fixed/yaml-standards.yaml',
        ];
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();

        foreach ($pathToFiles as $key => $pathToFile) {
            $standardParametersData = $this->getStandardsParametersData();
            $result = $yamlAlphabeticalChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML spaces between groups check of "%s" failed.', $pathToFile));
        }
    }

    /**
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(): StandardParametersData
    {
        return new StandardParametersData(5, 4, 2, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false);
    }
}
