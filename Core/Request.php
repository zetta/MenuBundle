<?php
/**
 * @author Juan Carlos Clemente <zetaweb@gmail.com>
 */

namespace Zetta\MenuBundle\Core;


use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * Mock Request 
 *
 * @author zetta
 */
class Request extends BaseRequest
{
    /**
     * pathinfo
     */
    protected $pathInfo;
    
    /**
     * Override the constructor, we dont need the init 
     */
    public function __construct()
    {
        
    }

    /**
     * Gets the value of pathInfo.
     *
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * Sets the value of pathInfo.
     *
     * @param mixed $pathInfo the pathInfo
     *
     * @return self
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }
}



