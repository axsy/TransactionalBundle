<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class MethodLevelAnnotatedService
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