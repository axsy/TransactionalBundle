<?php

namespace Axsy\TransactionalBundle\Tests\Resolver;

use Axsy\TransactionalBundle\Resolver\ConnectionResolver;

class ConnectionResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldResolveConnection()
    {
        // given

        $resolver = $this->getResolver();
        $metadata = $this->getMethodMetadata();
        $metadata->connection = 'default';

        // when

        $conn = $resolver->resolve($metadata);

        // then

        $this->assertInstanceOf('\\Doctrine\\DBAL\\Connection', $conn);
    }

    protected function getResolver()
    {
        $conn = $this
            ->getMockBuilder('Doctrine\\DBAL\\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $registry = $this
            ->getMockBuilder('Doctrine\\Bundle\\DoctrineBundle\\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $registry
            ->expects($this->once())
            ->method('getConnection')
            ->with($this->equalTo('default'))
            ->will($this->returnValue($conn));

       return new ConnectionResolver($registry);
    }

    protected function getMethodMetadata()
    {
        return $this
            ->getMockBuilder('Axsy\\TransactionalBundle\\Metadata\\MethodMetadata')
            ->disableOriginalConstructor()
            ->getMock();
    }
}