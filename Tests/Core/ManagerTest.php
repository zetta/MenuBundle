<?php


namespace Zetta\MenuBundle\Tests\Core;

use Zetta\MenuBundle\Core\Manager;


class ManagerTest extends \PHPUnit_Framework_TestCase
{

    protected $container;
    protected $loader;

    public function setUp()
    {
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->manager = new Manager(
            $container,
            $factory,
            $this->getMock('Zetta\MenuBundle\Services\SecurityInterface'),
            $this->getSampleConfig()
        );
        $factory->expects($this->any())
            ->method('createFromArray')
            ->will($this->returnValue( $this->getMock('\Knp\Menu\ItemInterface') ));
        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValue( $this->getMock('\Symfony\Component\HttpFoundation\Request') ));
    }

    public function testHas()
    {
        $this->assertTrue($this->manager->has('menu1'));
        $this->assertTrue($this->manager->has('menu2'));
        $this->assertTrue($this->manager->has('menu3'));
        $this->assertFalse($this->manager->has('menu4'));
    }

    public function testGetMenu()
    {
        $menu = $this->manager->getMenu('menu1');
        $this->assertInstanceOf('\Knp\Menu\ItemInterface', $menu);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMenuThrowsExceptionUnlessMenuIsDefined()
    {
        $this->manager->getMenu('menu4');
    }



    /**
     * getSampleConfig
     *
     * @return array
     */
    protected function getSampleConfig()
    {
        return array(
            'menus' => array(
                'menu1' => array(
                    'node1' => array('label' => 'Label', 'uri' => '/uri/', 'route' => null)
                ),
                'menu2' => array(
                    'node1' => array('label' => 'Label', 'uri' => '/uri/', 'route' => null)
                ),
                'menu3' => array(
                    'node1' => array('label' => 'Label', 'uri' => '/uri/', 'route' => null)
                ),
            ),
        );
    }


}