ZettaMenuBundle
===============


One Menu To Rule Them All

Este bundle es una extensión de [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) con el cual puedes agregar un menú general para el sistema y este se filtrará dependiendo el rol del usuario.

## Caracterísiticas

 - Los menus son definidos desde el archivo de configuración
 - Si cambias el firewall de tu sistema el menu se modifica automáticamente para no mostrar links a secciones no permitidas
 - Acepta seguridad via [Anotaciones](http://jmsyst.com/bundles/JMSSecurityExtraBundle/master/annotations#secure)


## Requisitos

 - [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)
 - [KnpMenu](https://github.com/KnpLabs/KnpMenu) y el bundle
 - [JMSSecurityExtraBundle](https://github.com/schmittjoh/JMSSecurityExtraBundle)


## Instalación

Para instalar el bundle es necesario agregar la dependencia en el archivo composer.json.

```json
    //composer.json
    "require": {
        "zetta/menu-bundle" : "dev-master"
    }
```

Posteriormente se debe registrar el bundle en el kernel de la aplicación

```php
// app/AppKernel.php
    $bundles = array(
        ....
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Zetta\MenuBundle\ZettaMenuBundle()
```

## Modo de Uso


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


### Anotaciones

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