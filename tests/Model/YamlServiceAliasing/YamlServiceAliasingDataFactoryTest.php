<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlServiceAliasingDataFactoryTest extends TestCase
{
    public function testYamlLinesHasServicesInHighestParent(): void
    {
        $yamlLines = ['services:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo', '                fred: plugh'];

        $hasServices = YamlServiceAliasingDataFactory::existsServicesInHighestParent($yamlLines);

        $this->assertTrue($hasServices);
    }

    public function testYamlLinesDoesNotHaveServicesInHighestParent(): void
    {
        $yamlLines = ['foo:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo', '                fred: plugh'];

        $hasServices = YamlServiceAliasingDataFactory::existsServicesInHighestParent($yamlLines);

        $this->assertFalse($hasServices);
    }

    public function testGetCorrectShortYamlLines(): void
    {
        $yamlContent = 'foo:
    bar: baz
    qux: quux
services:
    firstNameOfService: \'@secondNameOfService\'
    secondNameOfService:
        alias: thirdNameOfService
        public: false
    thirdNameOfService:
        arguments:
            $foo: \'@bar\'
    fourthNameOfService:
        alias: thirdNameOfService';
        $yamlLines = ['foo:', '    bar: baz', '    qux: quux', 'services:', '    firstNameOfService: \'@secondNameOfService\'', '    secondNameOfService:', '        alias: thirdNameOfService', '        public: false', '    thirdNameOfService:', '        arguments:', '            $foo: \'@bar\'', '    fourthNameOfService:', '        alias: thirdNameOfService'];
        $correctYamlLines = ['foo:', '    bar: baz', '    qux: quux', 'services:', '    firstNameOfService: \'@secondNameOfService\'', '    secondNameOfService:', '        alias: thirdNameOfService', '        public: false', '    thirdNameOfService:', '        arguments:', '            $foo: \'@bar\'', '    fourthNameOfService: \'@thirdNameOfService\''];

        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
        $yamlParsedData = Yaml::parse($yamlContent);
        $yamlLines = YamlServiceAliasingDataFactory::getCorrectYamlLines($yamlLines, $yamlParsedData, $standardParametersData);

        $this->assertSame($correctYamlLines, array_values($yamlLines));
    }

    public function testGetCorrectLongYamlLines(): void
    {
        $yamlContent = 'foo:
    bar: baz
    qux: quux
services:
    firstNameOfService: \'@secondNameOfService\'
    secondNameOfService:
        alias: thirdNameOfService
        public: false
    thirdNameOfService:
        arguments:
            $foo: \'@bar\'
    fourthNameOfService:
        alias: thirdNameOfService';
        $yamlLines = ['foo:', '    bar: baz', '    qux: quux', 'services:', '    firstNameOfService: \'@secondNameOfService\'', '    secondNameOfService:', '        alias: thirdNameOfService', '        public: false', '    thirdNameOfService:', '        arguments:', '            $foo: \'@bar\'', '    fourthNameOfService:', '        alias: thirdNameOfService'];
        $correctYamlLines = ['foo:', '    bar: baz', '    qux: quux', 'services:', '    firstNameOfService:', '        alias: secondNameOfService', '    secondNameOfService:', '        alias: thirdNameOfService', '        public: false', '    thirdNameOfService:', '        arguments:', '            $foo: \'@bar\'', '    fourthNameOfService:', '        alias: thirdNameOfService'];

        $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG);
        $yamlParsedData = Yaml::parse($yamlContent);
        $yamlLines = YamlServiceAliasingDataFactory::getCorrectYamlLines($yamlLines, $yamlParsedData, $standardParametersData);

        $this->assertSame($correctYamlLines, array_values($yamlLines));
    }

    public function testLineBelongToServices(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('belongLineToServices');
        $reflectionMethod->setAccessible(true);

        $yamlLines = ['services:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo'];

        $belongLineToServices = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, 2]);

        $this->assertTrue($belongLineToServices);
    }

    public function testLineIsNotToServices(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('belongLineToServices');
        $reflectionMethod->setAccessible(true);

        $yamlLines = ['   services:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo'];

        $belongLineToServices = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, 2]);

        $this->assertFalse($belongLineToServices);
    }

    public function testIsOppositeLongAlias(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isLineOppositeAliasByType');
        $reflectionMethod->setAccessible(true);

        $yamlLine = '        alias: YamlStandardsApp\Mail\PhpMailer';

        $isLineAlias = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLine, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT]);

        $this->assertTrue($isLineAlias);
    }

    public function testIsOppositeShortAlias(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isLineOppositeAliasByType');
        $reflectionMethod->setAccessible(true);

        $yamlLine = '    app.mailer: \'@YamlStandardsApp\Mail\PhpMailer\'';

        $isLineAlias = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLine, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG]);

        $this->assertTrue($isLineAlias);
    }

    public function testAliasIsStandaloneForShortType(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isAliasStandalone');
        $reflectionMethod->setAccessible(true);

        $yamlContent = 'services:
    app.mailer:
        alias: YamlStandardsApp\Mail\PhpMailer';
        $yamlLines = ['services:', '    app.mailer:', '        alias: YamlStandardsApp\Mail\PhpMailer'];

        $yamlParsedData = Yaml::parse($yamlContent);
        $isAliasStandalone = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, $yamlParsedData, 2, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT]);

        $this->assertTrue($isAliasStandalone);
    }

    public function testAliasIsNotStandaloneForShortType(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isAliasStandalone');
        $reflectionMethod->setAccessible(true);

        $yamlContent = 'services:
    app.mailer:
        alias: YamlStandardsApp\Mail\PhpMailer
        public: true';
        $yamlLines = ['services:', '    app.mailer:', '        alias: YamlStandardsApp\Mail\PhpMailer', '        public: true'];

        $yamlParsedData = Yaml::parse($yamlContent);
        $isAliasStandalone = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, $yamlParsedData, 2, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT]);

        $this->assertFalse($isAliasStandalone);
    }

    public function testAliasIsStandaloneForLongType(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isAliasStandalone');
        $reflectionMethod->setAccessible(true);

        $yamlContent = 'services:
    app.mailer: \'@foo\'';
        $yamlLines = ['services:', '    app.mailer: \'@foo\''];

        $yamlParsedData = Yaml::parse($yamlContent);
        $isAliasStandalone = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, $yamlParsedData, 1, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG]);

        $this->assertTrue($isAliasStandalone);
    }

    public function testAliasIsNotStandaloneForLongType(): void
    {
        $reflectionClass = new ReflectionClass(YamlServiceAliasingDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('isAliasStandalone');
        $reflectionMethod->setAccessible(true);

        $yamlContent = 'services:
    app.mailer: 
        arguments:
            $foo: \'@bar\'';
        $yamlLines = ['services:', '    app.mailer:', '        arguments:', '            $foo: \'@bar\''];

        $yamlParsedData = Yaml::parse($yamlContent);
        $isAliasStandalone = $reflectionMethod->invokeArgs(new YamlServiceAliasingDataFactory(), [$yamlLines, $yamlParsedData, 3, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG]);

        $this->assertFalse($isAliasStandalone);
    }

    /**
     * @param string $aliasingType
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $aliasingType): StandardParametersData
    {
        return new StandardParametersData(4, 4, 2, $aliasingType, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false);
    }
}
