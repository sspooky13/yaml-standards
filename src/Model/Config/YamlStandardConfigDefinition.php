<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

class YamlStandardConfigDefinition implements ConfigurationInterface
{
    public const CONFIG_PATHS_TO_CHECK = 'pathsToCheck';
    public const CONFIG_EXCLUDED_PATHS = 'excludedPaths';
    public const CONFIG_CHECKERS = 'checkers';
    public const CONFIG_PATH_TO_CHECKER = 'pathToChecker';
    public const CONFIG_PARAMETERS_FOR_CHECKER = 'parameters';
    public const CONFIG_PARAMETERS_DEPTH = 'depth';
    public const CONFIG_PARAMETERS_INDENTS = 'indents';
    public const CONFIG_PARAMETERS_LEVEL = 'level';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        // fix for Symfony 4.2 and newer versions
        if (Kernel::VERSION_ID >= 40200) {
            $treeBuilder = new TreeBuilder('yaml_standards_config');
            /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->root('yaml_standards_config');
        }

        $this->buildItemsNode($rootNode->arrayPrototype());

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function buildItemsNode(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->arrayNode(self::CONFIG_PATHS_TO_CHECK)->isRequired()->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(self::CONFIG_EXCLUDED_PATHS)
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(self::CONFIG_CHECKERS)
                    ->isRequired()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(self::CONFIG_PATH_TO_CHECKER)->defaultNull()->end()
                            ->arrayNode(self::CONFIG_PARAMETERS_FOR_CHECKER)
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode(self::CONFIG_PARAMETERS_DEPTH)->defaultNull()->end()
                                        ->scalarNode(self::CONFIG_PARAMETERS_INDENTS)->defaultNull()->end()
                                        ->scalarNode(self::CONFIG_PARAMETERS_LEVEL)->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
