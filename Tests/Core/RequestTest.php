<?php

namespace Zetta\MenuBundle\Tests\Core;

use Zetta\MenuBundle\Core\Request;
use Symfony\Component\Yaml\Parser;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * No le veo mucho caso, pero mas vale prevenir
     */
    public function testDefault()
    {

        $request = new Request();
        $request->setPathInfo('foo/bar/baz');
        $this->assertEquals('foo/bar/baz', $request->getPathInfo());
    }

}