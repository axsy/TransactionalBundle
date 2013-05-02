<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

class ActionTransactionalControlTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldNotRollbackOnUnannotatedAction()
    {
        $client = $this->createClient();

        $this->createDatabaseSchema();

        $client->request('get', '/test');

        $this->assertEquals('Hello, Kernel!', $client->getResponse()->getContent());
    }
}