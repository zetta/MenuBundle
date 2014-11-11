<?php

namespace Zetta\MenuBundle\Services;


interface SecurityInterface
{

    /**
     * Verify if the current user has permission to see the current link
     * @param array $arguments array with route and uri
     * @return boolean True if the user has permission
     * @uses Security::checkPermissions
     */
    public function checkPermissions(array $arguments);


}
