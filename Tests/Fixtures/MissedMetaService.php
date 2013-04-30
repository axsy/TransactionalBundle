<?php

namespace Axsy\TransactionalBundle\Tests\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class MissedMetaService extends MissedMetaParentService
{
    public function foo()
    {
    }
}

class MissedMetaParentService
{
    /**
     * @Transactionable
     */
    public function foo()
    {
    }
}