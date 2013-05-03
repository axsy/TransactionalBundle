<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

use Doctrine\DBAL\Connection;

class ActionTransactionalControlTest extends WebTestCase
{
    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldNotRollbackOnUnannotatedAction()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/do-not-perform-rollback-on-unannotated');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(1, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldRollbackOnAnnotatedAction()
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

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldRollbackOnAnnotatedActionOnCustomConnection()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/perform-rollback-on-annotation-with-custom-connection');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_other');
        $this->assertEquals(0, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldRollbackOnAnnotatedActionOnAllowedExceptions()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/perform-rollback-on-annotation-with-allowed-exceptions');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(0, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldNotRollbackOnAnnotatedActionOnAllowedExceptions()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/do-not-perform-rollback-on-annotation-with-allowed-exceptions');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(1, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldRollbackOnAnnotatedActionOnNotAllowedExceptions()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/perform-rollback-on-annotation-with-not-allowed-exceptions');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(0, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldNotRollbackOnAnnotatedActionOnNotAllowedExceptions()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        try {
            $client->request('get', '/do-not-perform-rollback-on-annotation-with-not-allowed-exceptions');
        } catch(\Exception $e) {
        }

        // then
        $em = $client->getContainer()->get('em_default');
        $this->assertEquals(1, $em->createQuery('SELECT COUNT(u) FROM TestBundle:Entity u')->getSingleScalarResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function shouldSetCustomIsolation()
    {
        // given
        $client = $this->createClient();
        $this->createDatabaseSchema();

        // when
        $client->request('get', 'get-isolation-level');
        $isolation = (int)$client->getResponse()->getContent();

        // then
        $this->assertEquals(Connection::TRANSACTION_READ_UNCOMMITTED, $isolation);
    }
}