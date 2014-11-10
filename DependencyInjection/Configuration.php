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

        $rootNode
            // ->beforeNormalization()
            //     ->always(function($config)
            //     {
            //         if(isset($config['menus']))
            //         {
            //             foreach($config['menus'] as $idx => $node)
            //             {
            //                 $config['menus'][$idx] = $this->flatNode($node);
            //             }
            //         }
            //         return $config;
            //     })
            // ->end()
            ->fixXmlConfig('menu', 'menus')
        ;
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

    /**
     * Flat a node
     * @param array node
     */
    private function flatNode(array $node, $parent = null)
    {
        foreach($node as $idx => $item)
        {
            if(!is_array($item))
            {
                $msg = sprintf('Invalid node "%s" under "%s"', $idx, $parent);
                $ex = new InvalidConfigurationException($msg);
                $ex->setPath($parent);
                throw $ex;
            }
            $key = $parent?"${parent}.${idx}":$idx;
            $node[ $key ] = $item;
            if($key != $idx ) unset($node[$idx]);
            $node[ $key ]['parent'] = $parent;
            $node[ $key ]['uri'] = isset($node[ $key ]['uri'])?$node[ $key ]['uri']:null;
            $node[ $key ]['route'] = isset($node[ $key ]['route'])?$node[ $key ]['route']:null;
            $node[ $key ]['routeParameters'] = isset($node[ $key ]['routeParameters'])?$node[ $key ]['routeParameters']:null;
            if(array_key_exists('children', $item))
            {
                if(!is_array($item['children']))
                {
                    $msg = sprintf('Invalid node "%s" under "%s"', 'children', $key);
                    $ex = new InvalidConfigurationException($msg);
                    $ex->setPath($parent);
                    throw $ex;
                }
                $node = array_merge($node, $this->flatNode($item['children'], $key) );
                unset($node[$key]['children']);
            }
        }
        return $node;
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
