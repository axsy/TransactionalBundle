<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class IncorrectAnnotationDefinitionService
{
    /**
     * @Transactionable(rollbackFor="SomeException",noRollbackFor="SomeOtherException")
     */
    public function cantBeAnalysedProperly()
    {

    }
}