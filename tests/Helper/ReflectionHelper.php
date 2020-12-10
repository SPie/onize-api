<?php

namespace Tests\Helper;

/**
 * Trait ReflectionHelper
 *
 * @package Tests\Helper
 */
trait ReflectionHelper
{
    /**
     * @param $object
     *
     * @return \ReflectionObject
     */
    private function getReflectionObject($object): \ReflectionObject
    {
        return new \ReflectionObject($object);
    }

    /**
     * @param mixed  $object
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    private function runPrivateMethod($object, string $methodName, array $arguments = [])
    {
        $method = $this->getReflectionObject($object)->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke($object, $arguments);
    }
}
