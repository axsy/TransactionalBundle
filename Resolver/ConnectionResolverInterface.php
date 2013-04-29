<?php

namespace Axsy\TransactionalBundle\Resolver;

interface ConnectionResolverInterface
{
    public function resolve(MethodMetadata $method);
}