<?php

namespace Axsy\TransactionalBundle\Tests\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class HierarchicalService extends HierarchicalParentService
{
    /**
     * @Transactionable
     */
    public function boo()
    {
    }

    public function doo()
    {
    }
}

class HierarchicalParentService
{
    public function foo()
    {
    }

    /**
     * @Transactionable
     */
    public function bar()
    {
    }
}