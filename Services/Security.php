<?php

namespace Zetta\MenuBundle\Services;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\AccessMapInterface;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use JMS\SecurityExtraBundle\Metadata\Driver\AnnotationDriver;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

use Zetta\MenuBundle\Core\Request;
use \ReflectionClass;



/**
 * Description of RouteTester Service
 *
 * @author zetta
 */
class Security implements SecurityInterface{

    private $router;
    private $accessDecisionManager;
    private $context;
    private $request;
    private $securityAnnotationDriver;

    /**
     * Constructor de la clase
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver $resolver
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface $accessDecisionManager
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(RouterInterface $router,  AccessDecisionManagerInterface $accessDecisionManager, SecurityContextInterface $context,  AccessMapInterface $map, AnnotationDriver $securityAnnotationDriver ) {
        $this->router = $router;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->context = $context;
        $this->map = $map;
        $this->securityAnnotationDriver = $securityAnnotationDriver;
    }

    /**
     * Verify if the current user has permission to see the current link
     * @param array $arguments array with route and uri
     * @return boolean True if the user has permission
     * @uses Security::checkPermissionsForUri
     */
    public function checkPermissions(array $arguments)
    {
        if(!array_key_exists('uri', $arguments) || !array_key_exists('route', $arguments))
        {
            throw new \InvalidArgumentException("An array matching [uri=>'foo', 'route'=>'bar'] must be provide");
        }

        if(isset($arguments['route']))
        {
            $arguments['uri'] = $this->router->generate($arguments['route'], isset($arguments['routeParameters'])?$arguments['routeParameters']:[]);
        }

        return $this->checkPermissionsForUri($arguments['uri']);
    }

    /**
     * Check if a user has permission to a uri
     * @param string $uri
     * @return boolean
     */
    public function checkPermissionsForUri($uri)
    {
        if (!$token = $this->context->getToken()){
           throw new AuthenticationCredentialsNotFoundException('A Token was not found in the SecurityContext.');
        }
        $request = new Request();
        $request->setPathInfo($uri);
        list($attributes) = $this->map->getPatterns($request);

        if (null === $attributes){
            $route = $this->getRouteByUri($uri);
            if(null === $route)
            {
                return true;
            }

            list( $controllerName, $actionName ) = explode('::',$route->getDefault('_controller'));

            $metadata = $this->securityAnnotationDriver->loadMetadataForClass( new ReflectionClass($controllerName) );
            if(isset( $metadata->methodMetadata[$actionName] ))
            {
                $attributes = $metadata->methodMetadata[$actionName]->roles;
            }  else {
                return true;
            }
        }

        return $this->accessDecisionManager->decide($token, $attributes);

    }

    /**
     * Obtains a Route by uri (pathinfo)  /path/
     * @param string $uri
     * @return Route|null
     */
    protected function getRouteByUri($uri)
    {
        $matcher = new TraceableUrlMatcher($this->router->getRouteCollection(), $this->router->getContext());
        foreach ($matcher->getTraces($uri) as  $trace){
            if (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
                return $this->router->getRouteCollection()->get($trace['name']);
            }
        }
    }


}
