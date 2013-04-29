<?php

namespace Axsy\TransactionalBundle\Resolver;

use Axsy\TransactionalBundle\Metadata\MethodMetadata;
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
        return $this->registry->getConnection($method->connection);
    }
}