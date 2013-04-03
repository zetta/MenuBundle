<?php
/**
 * @author zetta <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class ZettaMenuExtension extends Extension
{
    /**
     * Bundle's configuration loader
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // if the user add a menu in the configuration
        $config = array();
        if(count($configs[0]))
        {
            $config = $this->processConfiguration(new Configuration(), $configs);

            $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');

            if (!isset($config)) {
                throw new \InvalidArgumentException('A menu must be configured');
            }
        }
        $container->setParameter('zetta_menu.menu.configuration', $config);
    }
}
