<?php

namespace Zetta\MenuBundle\Twig;

use Knp\Menu\Twig\Helper as KnpHelper;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\MenuFactory;


/**
 * Helper class containing logic to retrieve and render menus from templating engines
 *
 */
class Helper
{
    private $knpHelper;
    private $factory;
    private $matcher;

    /**
     * @param \Knp\Menu\Twig\Helper $rendererProvider
     */
    public function __construct(KnpHelper $knpHelper, MenuFactory $factory, Matcher $matcher)
    {
        $this->knpHelper = $knpHelper;
        $this->factory = $factory;
        $this->matcher = $matcher;
    }

    /**
     * Retrieves item in the menu, eventually using the menu provider.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException when the path is invalid
     * @throws \BadMethodCallException when there is no menu provider and the menu is given by name
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        $menu = $this->knpHelper->get($menu, $path, $options);
        $breadCrumb = $this->factory->createItem('breadCrumb');

        $treeIterator = new \RecursiveIteratorIterator(
            new \Knp\Menu\Iterator\RecursiveItemIterator(
                new \ArrayIterator(array($menu))
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );


        $iterator = new CurrentItemFilterIterator($treeIterator, $this->matcher);

        $end = null;
        foreach ($iterator as $item) {
           $end = $item;
           break;
        }

        if(null === $end)
            return null;

        $crumbs = array();
        while($end->getParent() !== null)
        {
            $crumbs[] = $end;
            $end = $end->getParent();
        }

        foreach (array_reverse($crumbs) as $crumb) {
            $breadCrumb->addChild( $crumb->copy() );
        }
        return $breadCrumb;
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * If the argument is an array, it will follow the path in the tree to
     * get the needed item. The first element of the array is the whole menu.
     * If the menu is a string instead of an ItemInterface, the provider
     * will be used.
     *
     * @throws \InvalidArgumentException
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param array $options
     * @param string $renderer
     * @return string
     */
    public function render($menu, array $options = array(), $renderer =  null)
    {
        if (!$menu instanceof ItemInterface) {
            $path = array();
            if (is_array($menu)) {
                if (empty($menu)) {
                    throw new \InvalidArgumentException('The array cannot be empty');
                }
                $path = $menu;
                $menu = array_shift($path);
            }

            $menu = $this->get($menu, $path);
        }

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }
}
