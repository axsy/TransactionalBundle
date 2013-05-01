<?php

namespace Axsy\TransactionalBundle\Interception;

use CG\Core\ClassUtils;
use JMS\AopBundle\Aop\PointcutInterface;
use Metadata\MetadataFactory;

class TransactionPointcut implements PointcutInterface
{
    protected $factory;

    public function __construct(MetadataFactory $factory)
    {
        $this->factory = $factory;
    }

    function matchesClass(\ReflectionClass $class)
    {
        return true;
    }

    function matchesMethod(\ReflectionMethod $method)
    {
        $metadata = $this->factory->getMetadataForClass(ClassUtils::getUserClass($method->class));

        return !is_null($metadata) && isset($metadata->methodMetadata[$method->name]);
    }
}