<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Resolver;

use Axsy\TransactionalBundle\Metadata\MethodMetadata;
use Doctrine\Common\Persistence\ConnectionRegistry;

/**
 * Determines connection according to method metodata
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class ConnectionResolver implements ConnectionResolverInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ConnectionRegistry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param ConnectionRegistry $registry
     */
    public function __construct(ConnectionRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(MethodMetadata $method)
    {
        return $this->registry->getConnection($method->connection);
    }
}