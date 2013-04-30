<?php

namespace Axsy\TransactionalBundle\Tests\Metadata;

use Axsy\TransactionalBundle\Metadata\ClassMetadata;
use Axsy\TransactionalBundle\Metadata\MethodMetadata;
use Doctrine\DBAL\Connection;
use Metadata\MergeableInterface;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateMethodMetadata()
    {
        // given
        $classMetadata = $this->createClassMetadata();

        // when
        $methodMetadata = $classMetadata->createMethodMetadata('foo');

        // then
        $this->assertEquals($methodMetadata->connection, $classMetadata->connection);
        $this->assertEquals($methodMetadata->isolation, $classMetadata->isolation);
        $this->assertEquals($methodMetadata->exceptions, $classMetadata->exceptions);
        $this->assertEquals($methodMetadata->rollbackOnExceptions, $classMetadata->rollbackOnExceptions);
    }

    /**
     * @test
     */
    public function shouldAddMethodMetadata()
    {
        // given
        $classMetadata = $this->createClassMetadata();
        $methodMetadata = new MethodMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService', 'bar');

        // when
        $classMetadata->addMethodMetadata($methodMetadata);

        // then
        $this->assertEquals($methodMetadata, $classMetadata->methodMetadata[$methodMetadata->name]);
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Method Axsy\TransactionalBundle\Tests\Fixtures\SimpleService:baz() declared as transactionable but it can't be static
     */
    public function shouldNotAddMethodMetadataBecauseOfStatic()
    {
        // given
        $classMetadata = $this->createClassMetadata();
        $methodMetadata = new MethodMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService', 'baz');

        // when
        $classMetadata->addMethodMetadata($methodMetadata);

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Method Axsy\TransactionalBundle\Tests\Fixtures\SimpleService:boo() declared as transactionable but it can't be final
     */
    public function shouldNotAddMethodMetadataBecauseOfFinal()
    {
        // given
        $classMetadata = $this->createClassMetadata();
        $methodMetadata = new MethodMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService', 'boo');

        // when
        $classMetadata->addMethodMetadata($methodMetadata);

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Method Axsy\TransactionalBundle\Tests\Fixtures\SimpleService:doo() declared as transactionable but it can't be private
     */
    public function shouldNotAddMethodMetadataBecauseOfPrivate()
    {
        // given
        $classMetadata = $this->createClassMetadata();
        $methodMetadata = new MethodMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService', 'doo');

        // when
        $classMetadata->addMethodMetadata($methodMetadata);

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    /**
     * @test
     */
    public function shouldMergeDifferentMetadataInstances()
    {
        // given
        $classMetadata1 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalService');
        $methodMetadata1 = new MethodMetadata($classMetadata1->reflection->name, 'boo');
        $classMetadata1->addMethodMetadata($methodMetadata1);

        $classMetadata2 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalParentService');
        $methodMetadata2 = new MethodMetadata($classMetadata2->reflection->name, 'bar');
        $classMetadata2->addMethodMetadata($methodMetadata2);

        // when
        $classMetadata1->merge($classMetadata2);

        // then
        $mergedMetadataKeys = array_keys($classMetadata1->methodMetadata);
        sort($mergedMetadataKeys);

        $this->assertEquals(array('bar', 'boo'), $mergedMetadataKeys);
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Can merge with Axsy\TransactionalBundle\Metadata\ClassMetadata only, an instance of Axsy\TransactionalBundle\Tests\Metadata\MergeableMethodMetadata is given
     */
    public function shouldFailOnMetadataMergeBecauseOfDifferentMetadataType()
    {
        // given
        $classMetadata = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalService');
        $methodMetadata = new MergeableMethodMetadata($classMetadata->reflection->name, 'boo');

        // when
        $classMetadata->merge($methodMetadata);

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Overriden method Axsy\TransactionalBundle\Tests\Fixtures\MissedMetaService:foo() doesn't repeat parent's transactionable annotation
     */
    public function shouldFailOnMetadataMergeBecauseOfMissedMetadataInChildClass()
    {
        // given
        $classMetadata1 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\MissedMetaService');
        $classMetadata2 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\MissedMetaParentService');
        $methodMetadata = new MergeableMethodMetadata($classMetadata2->reflection->name, 'foo');
        $classMetadata2->addMethodMetadata($methodMetadata);

        // when
        $classMetadata2->merge($classMetadata1);

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    /**
     * @test
     */
    public function shouldPassMetadataMergeBecauseOfSameMetadataInChildClass()
    {
        // given
        $classMetadata1 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\OverridenMetaService');
        $methodMetadata1 = new MethodMetadata($classMetadata1->reflection->name, 'foo');
        $methodMetadata1->connection = 'other';
        $classMetadata1->addMethodMetadata($methodMetadata1);

        $classMetadata2 = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\OverridenMetaParentService');
        $methodMetadata2 = new MethodMetadata($classMetadata2->reflection->name, 'foo');
        $methodMetadata2->connection = 'other';
        $classMetadata2->addMethodMetadata($methodMetadata2);

        // when
        $classMetadata2->merge($classMetadata1);

        // then
        $this->assertEquals(array('foo'), array_keys($classMetadata2->methodMetadata));
    }

    /**
     * @test
     */
    public function shouldNotDetermineGlobalParams()
    {
        // given
        $classMetadata = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService');

        // when
        $hasGlobalParams = $classMetadata->hasGlobalParams();

        // then
        $this->assertFalse($hasGlobalParams);
    }

    /**
     * @test
     */
    public function shouldDetermineGlobalParams()
    {
        // given
        $classMetadata = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService');
        $classMetadata->connection = 'other';

        // when
        $hasGlobalParams = $classMetadata->hasGlobalParams();

        // then
        $this->assertTrue($hasGlobalParams);
    }

    protected function createClassMetadata()
    {
        $classMetadata = new ClassMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService');
        $classMetadata->connection = 'default';
        $classMetadata->isolation = Connection::TRANSACTION_READ_COMMITTED;
        $classMetadata->exceptions[] = 'SomeException';
        $classMetadata->rollbackOnExceptions = true;

        return $classMetadata;
    }
}

// Used by 'shouldFailOnMetadataMergeBecauseOfDifferentMetadataType'
class MergeableMethodMetadata extends MethodMetadata implements MergeableInterface
{
    public function merge(MergeableInterface $object)
    {
    }
}