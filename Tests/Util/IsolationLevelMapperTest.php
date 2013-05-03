<?php

namespace Axsy\TransactionalBundle\Tests\Util;

use Axsy\TransactionalBundle\Util\IsolationLevelMapper;
use Doctrine\DBAL\Connection;

class IsolationLevelMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnCodeForReadUncommittedLevel()
    {
        // given
        $isolationLevel = 'read_uncommitted';

        // when
        $isolationLevelCode = IsolationLevelMapper::getCode($isolationLevel);

        // then
        $this->assertEquals(Connection::TRANSACTION_READ_UNCOMMITTED, $isolationLevelCode);
    }

    /**
     * @test
     */
    public function shouldReturnCodeForReadCommittedLevel()
    {
        // given
        $isolationLevel = 'read_committed';

        // when
        $isolationLevelCode = IsolationLevelMapper::getCode($isolationLevel);

        // then
        $this->assertEquals(Connection::TRANSACTION_READ_COMMITTED, $isolationLevelCode);
    }

    /**
     * @test
     */
    public function shouldReturnCodeForRepeatableReadLevel()
    {
        // given
        $isolationLevel = 'repeatable_read';

        // when
        $isolationLevelCode = IsolationLevelMapper::getCode($isolationLevel);

        // then
        $this->assertEquals(Connection::TRANSACTION_REPEATABLE_READ, $isolationLevelCode);
    }

    /**
     * @test
     */
    public function shouldReturnCodeForSerializableLevel()
    {
        // given
        $isolationLevel = 'serializable';

        // when
        $isolationLevelCode = IsolationLevelMapper::getCode($isolationLevel);

        // then
        $this->assertEquals(Connection::TRANSACTION_SERIALIZABLE, $isolationLevelCode);
    }
}