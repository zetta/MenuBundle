<?php

namespace Zetta\MenuBundle\Tests\DependencyInjection;

use Zetta\MenuBundle\DependencyInjection\ZettaMenuExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
//use Symfony\Component\DependencyInjection\Definition;
//use Symfony\Component\DependencyInjection\Parameter;
//use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

class ZettaMenuExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $loader = new ZettaMenuExtension();
        $loader->load(array(array()), $container);
        $this->assertTrue($container->hasParameter('zetta_menu.menu.configuration'), 'No existe configuracion para menu');

    }


    public function testLoadConfiguration()
    {
        $container = new ContainerBuilder();
        $loader = new ZettaMenuExtension();
        $config = $this->getSampleConfig();
        $loader->load(array($config), $container);
        $this->assertTrue($container->hasParameter('zetta_menu.menu.configuration'), 'No existe configuracion para menu');

        $this->assertTrue($container->hasDefinition('zetta_menu.security'));
        $this->assertTrue($container->hasDefinition('zetta_menu.manager'));
        $this->assertTrue($container->hasDefinition('zetta_menu.provider'));

    }

     /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionUnlessCorrectMenuIsDefined()
    {
        $container = new ContainerBuilder();
        $loader = new ZettaMenuExtension();
        $config = $this->getIncorrectConfig();
        $loader->load(array($config), $container);
    }




    /**
     * getSampleConfig
     *
     * @return array
     */
    protected function getSampleConfig()
    {
        $yaml = "
menus:
    menu1:
        node1:
            label: 'Label'
            uri: '/uri/'
        ";
        $parser = new Parser();
        return $parser->parse($yaml);
    }


    /**
     * getInvalidConfig
     *
     * @return array
     */
    protected function getInvalidConfig()
    {
        $yaml = "
menus:
    menu1:
        node1:
            incorrect: 'Label'
            uri: '/uri/'
        ";
        $parser = new Parser();
        return $parser->parse($yaml);
    }

}
