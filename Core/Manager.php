<?php
/**
 * @author Juan Carlos Clemente <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\Core;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zetta\MenuBundle\Services\SecurityInterface;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Iterator\RecursiveItemIterator;
use \RecursiveIteratorIterator;

class Manager
{

    private $container;
    private $factory;
    private $config;
    private $security;
    private $matcher;


    /**
     * @param ContainerInterface $container
     * @param FactoryInterface $factory
     * @param array $config
     */
    public function __construct(ContainerInterface $container, FactoryInterface $factory,  SecurityInterface $security, Matcher $matcher,  array $config )
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->config = $config;
        $this->security = $security;
        $this->matcher = $matcher;
        if($container->isScopeActive('request'))
        {
            $this->matcher->addVoter(new UriVoter( $container->get('request')->getPathInfo() ));
        }

    }

    /**
     * Builds the requested menu
     *
     * @param string $name
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function getMenu($name, array $options = array())
    {
        if(!$this->has($name, $options))
        {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        $nodes = $this->config['menus'][$name];
        foreach ($nodes as $idx => $node)
        {
            if(!$this->security->checkPermissions(['uri' => $node['uri'], 'route' => $node['route'], 'routeParameters' => $node['routeParameters']]))
            {
                //user cant see this node
                unset($nodes[ $idx ]);
                continue;
            }
            if($node['parent'])
            {
                $location = &$nodes;
                $found = false;
                foreach(explode('.', $node['parent']) as $step)
                {
                    if(isset($location[$step]))
                    {
                        $location = &$location[$step]['children'];
                        $found = true;
                    }else
                    {
                        $found = false;
                    }
                }
                // if parent node are hidden childs dont display
                if($found)
                {
                    $location[str_replace($node['parent'].'.', '', $idx)] = $node;
                    $node['parent'] = null;
                }
                unset($nodes[ $idx ]);
            }
        }

        // Build the menu

        $menu = $this->factory->createFromArray(array('children' => $nodes));

        $itemIterator = new RecursiveItemIterator($menu);
        $iterator = new RecursiveIteratorIterator($itemIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item)
        {
            if (is_array($item->getExtra('routes')))
            {
                if ($this->matcher->isCurrent($item) || in_array($this->container->get('request')->get('_route'), $item->getExtra('routes')) )
                {
                    $item->setCurrent(true);
                }
            }
        }
        return $menu;
    }

    /**
     * Checks if the specified menu exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name, array $options = array())
    {
        return isset($this->config['menus']) && array_key_exists($name, $this->config['menus']);
    }
}