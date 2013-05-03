<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

class ActionTransactionalControlTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldNotRollbackOnUnannotatedAction()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/perform-rollback-on-defaults');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(0, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }
}