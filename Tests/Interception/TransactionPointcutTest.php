<?php

namespace Axsy\TransactionalBundle\Tests\Interception;

use Axsy\TransactionalBundle\Interception\TransactionPointcut;
use Axsy\TransactionalBundle\Metadata\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\MetadataFactory;

class TransactionPointcutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldMathMethod()
    {
        // given
        $factory = $this->getMetadataFactory();
        $pointcut = new TransactionPointcut($factory);

        $methodFoo = new \ReflectionMethod('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalService', 'foo');
        $methodBar = new \ReflectionMethod('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalService', 'bar');
        $methodBoo = new \ReflectionMethod('Axsy\\TransactionalBundle\\Tests\\Fixtures\\HierarchicalService', 'boo');

        // when
        $matchesFoo = $pointcut->matchesMethod($methodFoo);
        $matchesBar = $pointcut->matchesMethod($methodBar);
        $matchesBoo = $pointcut->matchesMethod($methodBoo);

        // then
        $this->assertFalse($matchesFoo);
        $this->assertTrue($matchesBar);
        $this->assertTrue($matchesBoo);
    }

    protected function getMetadataFactory()
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader(), 'default'));
        $factory->setIncludeInterfaces(true);

        return $factory;
    }
}