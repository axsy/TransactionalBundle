<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Resolver;

use Axsy\TransactionalBundle\Metadata\MethodMetadata;

/**
 * Determines connection according to method metodata
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
interface ConnectionResolverInterface
{
    /**
     * @param MethodMetadata $method
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function resolve(MethodMetadata $method);
}