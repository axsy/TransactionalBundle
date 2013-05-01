<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;
use Doctrine\DBAL\Connection;

/**
 * @Transactionable(
 *     connection="other",
 *     rollbackFor={
 *         "FirstException",
 *         "SecondException"
 *     },
 *     isolation=Connection::TRANSACTION_READ_UNCOMMITTED
 * )
 */
class ClassLevelRollbackAnnotatedService
{
    public function foo()
    {
    }
}