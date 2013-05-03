<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Interception;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Axsy\TransactionalBundle\Resolver\ConnectionResolver;
use Metadata\MetadataFactory;

/**
 * Implements an around advice for the transactional behavior
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class MethodTransactionalInterceptor implements MethodInterceptorInterface
{
    /**
     * @var \Metadata\MetadataFactory
     */
    protected $factory;

    /**
     * @var \Axsy\TransactionalBundle\Resolver\ConnectionResolver
     */
    protected $resolver;

    /**
     * Constructor
     *
     * @param MetadataFactory $factory
     * @param ConnectionResolver $resolver
     */
    public function __construct(MetadataFactory $factory, ConnectionResolver $resolver)
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
    }

    /**
     * Called when intercepting the method call
     *
     * @param MethodInvocation $invocation
     * @return mixed
     *
     * @throws \Exception
     */
    function intercept(MethodInvocation $invocation)
    {
        $metadata = $this->factory->getMetadataForClass($invocation->reflection->class);
        /** @var $methodMetadata \Axsy\TransactionalBundle\Metadata\MethodMetadata */
        $methodMetadata = $metadata->methodMetadata[$invocation->reflection->name];
        /** @var $conn \Doctrine\DBAL\Connection */
        $conn = $this->resolver->resolve($methodMetadata);

        try {
            $oldIsolation = $conn->getTransactionIsolation();
            $conn->beginTransaction();
            if ($oldIsolation != $methodMetadata->isolation) {
                $conn->setTransactionIsolation($methodMetadata->isolation);
            }
            $result = $invocation->proceed();
            $conn->commit();
            if ($oldIsolation != $methodMetadata->isolation) {
                $conn->setTransactionIsolation($oldIsolation);
            }
        } catch (\Exception $e) {
            if ($methodMetadata->haveToRollbackOn($e)) {
                $conn->rollBack();
            } else {
                $conn->commit();
            }
            if ($oldIsolation != $methodMetadata->isolation) {
                $conn->setTransactionIsolation($oldIsolation);
            }
            throw $e;
        }

        return $result;
    }
}