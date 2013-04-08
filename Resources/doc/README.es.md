ZettaMenuBundle
===============

One Menu To Rule Them All

Este bundle es una extensión de [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) con el cual puedes agregar un menú general para el sistema y este se filtrará dependiendo el rol del usuario.

## Características

 - Los menus pueden ser definidos desde el archivo de configuración `app/config/config.yml`
 - Los links del menu son filtrados automáticamente por las reglas definidas en el firewall
 - Las Anotaciones de [JMSSecurityExtraBundle](http://jmsyst.com/bundles/JMSSecurityExtraBundle/master/annotations#secure) son soportadas para la creación del filtro
 - Integración con los menus existentes en tu bundle gracias a la anotación [@SecureMenu](#mtodo-simple)


## Requisitos

 - PHP >=5.4
 - [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)
 - [KnpMenu](https://github.com/KnpLabs/KnpMenu)
 - [JMSSecurityExtraBundle](https://github.com/schmittjoh/JMSSecurityExtraBundle)


## Instalación

Para instalar el bundle es necesario agregar la dependencia en el archivo composer.json.

```json
    //composer.json
    "require": {
        "zetta/menu-bundle" : "1.1.*"
    }
```

Posteriormente se debe registrar el bundle en el kernel de la aplicación.

```php
// app/AppKernel.php
    $bundles = array(
        ....
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Zetta\MenuBundle\ZettaMenuBundle()
```

**Es importante que la declaración de ZettaMenuBundle sea después de KnpMenuBundle**

## Modo de Uso

Existen 2 formas de filtrar los menús que generamos.

### Método simple

Si utilizas el [método simple](https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/index.md#method-a-the-easy-way-yay) para la creación de menus

```php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Acme\DemoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('About Me', array(
            'route' => 'page_show',
            'routeParameters' => array('id' => 42)
        ));
        // ... add more children

        return $menu;
    }
}
```

Basta con agregar la Anotación `Zetta\MenuBundle\Annotation\SecureMenu` al método para filtrar los items.

```php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Acme\DemoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Zetta\MenuBundle\Annotation\SecureMenu;

class Builder extends ContainerAware
{
    /**
     * @SecureMenu
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('About Me', array(
            'route' => 'page_show',
            'routeParameters' => array('id' => 42)
        ));
        // ... add more children

        return $menu;
    }
}
```

### Método de configuración (config.yml)

Definimos un menú básico en el archivo de configuracion

```yaml
#app/config/config.yml
zetta_menu:
    menus:
        admin:
            dashboard:
                label: 'Dashboard'
                route: '_welcome'
            users:
                label: 'Usuarios'
                uri: '/user/'
                children:
                    new:
                        label: 'Guardar usuario nuevo'
                        uri: '/user/new'
                    archive:
                        label: 'Usuarios historicos'
                        uri: '/user/archive'
            catalogs:
                label: 'Catálogos'
                route: 'catalogs'
                children:
                    status:
                        label: 'Status'
                        uri: '/status/list'
            statistics:
                label: 'Estadísticas'
                uri: '/admin/stats'

        sidebar:  #otro menu ...
            sidebar1:
                label: "Sidebar 1"
```


Para imprimirlo en nuestro template utilizamos el helper de knp

```jinja
    {{ knp_menu_render('admin') }}
```

Por default si no existen reglas de denegación el menu se imprimirá completo.

 - Dashboard
 - Usuarios
    - Guardar usuario nuevo
    - Usuarios históricos
 - Catálogos
    - Status
 - Estadísticas


Al definir reglas de seguridad podemos observar como el render del menu se ve afectado.

```yaml
#app/config/security.yml
security:

    role_hierarchy:
        ROLE_MANAGER:       ROLE_USER
        ROLE_ADMIN:         ROLE_MANAGER
    ...
    access_control:
        - { path: ^/admin/, role: ROLE_ADMIN }
        - { path: ^/user/new, role: ROLE_MANAGER }
        - { path: ^/$, role: ROLE_USER }
```


El administrador de sistema podrá ver entonces el menú completo, sin embargo si un usuario con rol ROLE_USER entra al sistema el solo podrá ver:

 - Dashboard
 - Usuarios
    - Usuarios históricos
 - Catálogos
    - Status


#### Anotaciones

Teniendo la ruta de los catálogos definida

```yaml
#app/config/routing.yml
catalogs:
    pattern: /catalogs/
    defaults: {_controller: ExampleBundle:Catalogs:index}
```

Agregamos la anotación en el método de nuestro controlador

```php
// src/Acme/ExampleBundle/Controller/CatalogsController.php
use JMS\SecurityExtraBundle\Annotation\Secure;

class CatalogsController{

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function indexAction()
    {
        // ... blah
    }

}
```

El mismo rol ROLE_USER verá entonces un menu asi

 - Dashboard
 - Usuarios
    - Usuarios históricos