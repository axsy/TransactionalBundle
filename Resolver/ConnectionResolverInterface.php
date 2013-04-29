<?php

namespace Axsy\TransactionalBundle\Resolver;

use Axsy\TransactionalBundle\Metadata\MethodMetadata;

interface ConnectionResolverInterface
{
    public function resolve(MethodMetadata $method);
}