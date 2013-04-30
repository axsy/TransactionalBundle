<?php

namespace Axsy\TransactionalBundle\Tests\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class OverridenMetaService extends OverridenMetaParentService
{
    /**
     * @Transactional(connection="other")
     */
    public function foo()
    {
    }
}

class OverridenMetaParentService
{
    /**
     * @Transactional(connection="other")
     */
    public function foo()
    {
    }
}