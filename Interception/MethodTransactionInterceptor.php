<?php

namespace Axsy\TransactionalBundle\Interception;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Axsy\TransactionalBundle\Resolver\ConnectionResolver;
use Metadata\MetadataFactory;

class MethodTransactionalInterceptor implements MethodInterceptorInterface
{
    protected $factory;
    protected $resolver;

    public function __construct(MetadataFactory $factory, ConnectionResolver $resolver)
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
    }

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