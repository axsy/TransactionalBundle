<?php

namespace Axsy\TransactionalBundle\Tests\Fixtures;

use Axsy\TransactionalBundle\Annotation\Transactionable;

class SimpleService
{
    public function foo()
    {
    }

    /**
     * @Transactionable()
     */
    public function bar()
    {
    }

    public static function baz()
    {
    }

    final public function boo()
    {
        $this->doo();
    }

    private function doo()
    {
    }
}