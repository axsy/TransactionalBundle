<?php

namespace Axsy\TransactionalBundle\Resolver;

use Doctrine\Common\Persistence\ConnectionRegistry;

class ConnectionResolver implements ConnectionResolverInterface
{
    protected $registry;

    public function __construct(ConnectionRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function resolve(MethodMetadata $method)
    {
        $this->registry->getConnection($method->connection);
    }
}