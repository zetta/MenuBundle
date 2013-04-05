<?php


namespace Zetta\MenuBundle\Provider;

use Zetta\MenuBundle\Services\SecurityInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class SecureProvider
{

    private $security;
    private $reader;
    private $annotationClass = 'Zetta\\MenuBundle\\Annotation\\SecureMenu';

    public function __construct(Reader $reader, SecurityInterface $security)
    {
        $this->reader = $reader;
        $this->security = $security;
    }

    public function filter(ContainerAwareInterface $originalObject, $args)
    {
        $value = null;
        $reflectionObject = new \ReflectionObject($originalObject);
        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->annotationClass);
            if (null !== $annotation) {
                $value = $reflectionMethod->invokeArgs($originalObject, $args);
                $value = $annotation->filter($value, $this->security);
            }
        }
        return $value;
    }


}