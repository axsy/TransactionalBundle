<?php

namespace Axsy\TransactionalBundle\Tests\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class DifferentMetaService extends DifferentMetaParentService
{
    /**
     * @Transactional(connection="other")
     */
    public function foo()
    {
    }
}

class DifferentMetaParentService
{
    /**
     * @Transactional(connection="default")
     */
    public function foo()
    {
    }
}