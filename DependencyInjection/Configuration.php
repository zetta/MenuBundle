<?php
/**
 * @author zetta <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class Configuration implements ConfigurationInterface
{

    /**
     * Builds the config tree
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zetta_menu');

        $rootNode->fixXmlConfig('menu', 'menus');
        $this->buildMenuNode(
            $rootNode
            ->children()
                ->arrayNode('menus')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
        )
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    protected function buildMenuNode(NodeDefinition $node)
    {
        return $node
            ->children()
                ->scalarNode('label')->end()
                ->variableNode('linkAttributes')->end()
                ->variableNode('childrenAttributes')->end()
                ->variableNode('labelAttributes')->end()
                ->scalarNode('uri')->defaultNull()->end()
                ->variableNode('route')->defaultNull()->end()
                ->variableNode('routeParameters')->defaultNull()->end()
                ->booleanNode('display')->end()
                ->booleanNode('displayChildren')->end()
                ->variableNode('attributes')->end()
                ->variableNode('extras')->end()
                ->variableNode('children')
                    ->validate()->ifTrue(function($element) { return !is_array($element); })->thenInvalid('The children element must be an array.')->end()
                    ->validate()->always(function($children) {array_walk($children, array($this, 'evaluateChildren'));return $children;})->end()
                ->end()
        ;
    }

    protected function evaluateChildren(&$child, $name)
    {
        $child = $this->getPathNode($name)->finalize($child);
    }

    protected function getPathNode($name = '')
    {
        $treeBuilder = new TreeBuilder();
        $definition = $treeBuilder->root($name);
        $this->buildMenuNode($definition);
        return $definition->getNode(true);
    }

}
