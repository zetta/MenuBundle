<?php

namespace Zetta\MenuBundle\Annotation;

use Knp\Menu\MenuItem;
use Zetta\MenuBundle\Services\SecurityInterface;
use Knp\Menu\Iterator\RecursiveItemIterator;
use \RecursiveIteratorIterator;


/**
 * @Annotation
 */
final class SecureMenu
{

    public function __construct()
    {

    }

    public function filter(MenuItem $menu, SecurityInterface $security)
    {
        $itemIterator = new RecursiveItemIterator($menu);

        // iterate recursively on the iterator
        $iterator = new RecursiveIteratorIterator($itemIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
           if(! $security->checkPermissions([ 'uri' => $item->getUri(), 'route' => $item['route'] ]) )
           {

               $item->setDisplay(false);
           }

        }
        return $menu;
    }

}