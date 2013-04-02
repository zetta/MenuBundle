ZettaMenuBundle
===============


Bundle que utiliza KnpMenu para el rendereo de los menus y adicionalmente se administra la seguridad de esta manera puedes tener un único menú que se mostrará de diferentes formas dependiendo del ROL de usuario que esté navegando.




## Configuración

Para agregar un menu se debe agregar la entrada al archivo config 

-/app/config/config.yml-
```yaml
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
            domains:
                label: 'Dominios'
                uri: '/domain/list'
                children:
                   new:
                     label: Nuevo 
                     uri: '/domain/new'
            external:
                label: 'Externo'
                uri: 'http://www.google.com/'
            catalogs:
                label: 'Catalogos'
                route: 'catalogs'
                children:
                    status:
                        label: 'Status'
                        uri: '/status/list'
                    sex: 
                        label: 'Sexo'
                        uri: '/sex/list'
                    brands:
                        label: 'Marcas'
                        uri: '/brand/list'
                        children: 
                            new: 
                                label: 'Nueva Marca'
                                uri: '/brand/new'
                    places:
                        label: 'Lugares'
                        uri: '/places/list'
                        children:
                            new: 
                                label: 'Nuevo lugar'
                                uri: '/place/new'
            admin:
                label: 'Administracion'
                uri: '/admin/charts'

        sidebar:
            sidebar1: 
                label: "Sidebar 1"
```


