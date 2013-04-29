<?php

namespace Axsy\TransactionalBundle\Interception;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Axsy\TransactionalBundle\Resolver\ConnectionResolver;

class MethodTransactionalInterceptor implements MethodInterceptorInterface
{
    protected $resolver;

    public function __construct(ConnectionResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    function intercept(MethodInvocation $invocation)
    {
        // TODO: Implement intercept() method.
    }
}