<?php
/**
 * @author zetta <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Zetta\MenuBundle\Core\Manager;


class MenuProvider implements MenuProviderInterface
{
    
    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * @param FactoryInterface $factory the menu factory used to create the menu item
     */
    public function __construct(FactoryInterface $factory, Manager $manager)
    {
        $this->factory = $factory;
        $this->manager = $manager;
    }

    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
         return $this->manager->getMenu($name, $options);
        
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        return $this->manager->has($name, $options);
    }


}