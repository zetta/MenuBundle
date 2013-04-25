<?php
/**
 * @author zetta <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\ConfigurationInterface;


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
            ->beforeNormalization()
                ->always(function($config)
                {
                    if(isset($config['menus']))
                    {
                        foreach($config['menus'] as $idx => $node)
                        {
                            $config['menus'][$idx] = $this->flatNode($node);
                        }
                    }
                    return $config;
                })
            ->end()
            ->fixXmlConfig('menu', 'menus')
            ->children()
            ->arrayNode('menus')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                //->children()
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('parent')->isRequired()->end()
                        ->scalarNode('label')->isRequired()->end()
                        //->scalarNode('action')->end()
                        ->scalarNode('uri')->end()
                        ->variableNode('route')->end()
                        ->variableNode('extras')->end()
                        //->variableNode('children')->end()
                    ->end()
                ->end()
        ->end();


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

}
