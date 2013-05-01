<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

/**
 * @Transactionable(noRollbackFor={"NotFoundException"})
 */
class AnnotationHierarchyService extends AnnotationHierarchyParentService
{
    public function foo()
    {
    }

    /**
     * @Transactionable(connection="other")
     */
    public function bar()
    {
    }
}

class AnnotationHierarchyParentService
{
    /**
     * @Transactionable(connection="other")
     */
    public function bar()
    {
    }
}