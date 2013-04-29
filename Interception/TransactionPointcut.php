<?php

namespace Axsy\TransactionalBundle\Interception;

use JMS\AopBundle\Aop\PointcutInterface;

class TransactionPointcut implements PointcutInterface
{
    function matchesClass(\ReflectionClass $class)
    {
        // TODO: Implement matchesClass() method.
    }

    function matchesMethod(\ReflectionMethod $method)
    {
        // TODO: Implement matchesMethod() method.
    }
}