<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

/**
 * @Transactionable
 */
class ClassLevelAnnotatedService
{
    public function foo()
    {
    }
}