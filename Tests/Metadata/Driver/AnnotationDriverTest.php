<?php

namespace Axsy\TransactionalBundle\Tests\Metadata\Driver;

use Axsy\TransactionalBundle\Metadata\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;

class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldNoReturnMetadataForUnannotatedClass()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\UnannotatedService'));

        // then
        $this->assertNull($metadata);
    }

    /**
     * @test
     */
    public function shouldReturnMetadataInCaseOfClassLevelAnnotation()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\ClassLevelAnnotatedService'));

        // then
        $this->assertEquals(1, count($metadata->methodMetadata));

        $methodMetadata = $metadata->methodMetadata['foo'];

        $this->assertEquals('foo', $methodMetadata->name);
        $this->assertEquals('default', $methodMetadata->connection);
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $methodMetadata->isolation);
        $this->assertNull($methodMetadata->exceptions);
        $this->assertTrue($methodMetadata->rollbackOnExceptions);
    }

    /**
     * @test
     */
    public function shouldReturnMetadataInCaseOfClassLevelRollbackAnnotation()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\ClassLevelNoRollbackAnnotatedService'));

        // then
        $this->assertEquals(1, count($metadata->methodMetadata));

        $methodMetadata = $metadata->methodMetadata['foo'];

        $this->assertEquals('foo', $methodMetadata->name);
        $this->assertEquals('other', $methodMetadata->connection);
        $this->assertEquals(Connection::TRANSACTION_READ_UNCOMMITTED, $methodMetadata->isolation);
        $this->assertEquals(array('FirstException', 'SecondException'), $methodMetadata->exceptions);
        $this->assertFalse($methodMetadata->rollbackOnExceptions);
    }

    /**
     * @test
     */
    public function shouldReturnMetadataInCaseOfMethodLevelAnnotation()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\MethodLevelAnnotatedService'));

        // then
        $this->assertEquals(1, count($metadata->methodMetadata));

        $methodMetadata = $metadata->methodMetadata['bar'];

        $this->assertEquals('bar', $methodMetadata->name);
        $this->assertEquals('default', $methodMetadata->connection);
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $methodMetadata->isolation);
        $this->assertNull($methodMetadata->exceptions);
        $this->assertTrue($methodMetadata->rollbackOnExceptions);
    }

    /**
     * @test
     */
    public function shouldReturnMetadataInCaseOfAnnotationHierarchy()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\AnnotationHierarchyService'));

        // then
        $this->assertEquals(2, count($metadata->methodMetadata));

        $methodMetadata = $metadata->methodMetadata['foo'];

        $this->assertEquals('foo', $methodMetadata->name);
        $this->assertEquals('default', $methodMetadata->connection);
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $methodMetadata->isolation);
        $this->assertEquals(array('NotFoundException'), $methodMetadata->exceptions);
        $this->assertFalse($methodMetadata->rollbackOnExceptions);

        $methodMetadata = $metadata->methodMetadata['bar'];

        $this->assertEquals('bar', $methodMetadata->name);
        $this->assertEquals('other', $methodMetadata->connection);
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $methodMetadata->isolation);
        $this->assertEquals(array('NotFoundException'), $methodMetadata->exceptions);
        $this->assertFalse($methodMetadata->rollbackOnExceptions);
    }

    /**
     * @test
     * @expectedException Axsy\TransactionalBundle\Exception\LogicException
     * @expectedExceptionMessage Values for 'noRollbackFor' and 'rollbackFor' can't be provided at the same time
     */
    public function shouldThrowAnExceptionOnWrongDefinition()
    {
        // given
        $driver = $this->getAnnotationDriver();

        // when
        $metadata = $driver->loadMetadataForClass(
            new \ReflectionClass('Axsy\\TransactionalBundle\\Tests\\Metadata\\Driver\\Fixtures\\IncorrectAnnotationDefinitionService'));

        // then
        // @expectedException
        // @expectedExceptionMessage
    }

    public function getAnnotationDriver($connectionName = 'default')
    {
        return new AnnotationDriver(new AnnotationReader(), $connectionName);
    }
}