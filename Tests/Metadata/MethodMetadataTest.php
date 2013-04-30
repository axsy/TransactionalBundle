<?php

namespace Axsy\TransactionalBundle\Tests\Metadata;

use Axsy\TransactionalBundle\Metadata\MethodMetadata;

class MethodMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConfirmMetadataEquality()
    {
        // given
        $metadataFirst = $this->createMethodMetadata();
        $metadataSecond = clone $metadataFirst;

        // when
        $isEqual = $metadataFirst->equalTo($metadataSecond);

        // then
        $this->assertTrue($isEqual);
    }

    /**
     * @test
     */
    public function shouldNotConfirmMetadataEquality()
    {
        // given
        $metadataFirst = $this->createMethodMetadata();
        $metadataSecond = clone $metadataFirst;
        $metadataSecond->connection = 'not_default';

        // when
        $isEqual = $metadataFirst->equalTo($metadataSecond);

        // then
        $this->assertFalse($isEqual);
    }

    /**
     * @test
     */
    public function shouldRollbackOnException()
    {
        // given
        $metadata = $this->createMethodMetadata();
        $metadata->exceptions[] = 'SpecifiedException';
        $metadata->rollbackOnExceptions = true;

        // when
        $doRollbackOnSpecifiedException = $metadata->haveToRollbackOn('SpecifiedException');
        $doRollbackOnOtherException = $metadata->haveToRollbackOn('OtherException');

        // then
        $this->assertTrue($doRollbackOnSpecifiedException);
        $this->assertFalse($doRollbackOnOtherException);
    }

    /**
     * @test
     */
    public function shouldNoRollbackOnException()
    {
        // given
        $metadata = $this->createMethodMetadata();
        $metadata->exceptions[] = 'SpecifiedException';
        $metadata->rollbackOnExceptions = false;

        // when
        $doRollbackOnSpecifiedException = $metadata->haveToRollbackOn('SpecifiedException');
        $doRollbackOnOtherException = $metadata->haveToRollbackOn('OtherException');

        // then
        $this->assertFalse($doRollbackOnSpecifiedException);
        $this->assertTrue($doRollbackOnOtherException);
    }

    protected function createMethodMetadata()
    {
        return new MethodMetadata('Axsy\\TransactionalBundle\\Tests\\Fixtures\\SimpleService', 'foo');
    }
}