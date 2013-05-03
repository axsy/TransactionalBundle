<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Util;

/**
 * Maps string that represents isolation level to \Doctrine\DBAL\Connection constant value
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
final class IsolationLevelMapper {

    /**
     * Maps string that represents isolation level to \Doctrine\DBAL\Connection constant value
     *
     * @param string $isolation
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public static function getCode($isolation)
    {
        if (!in_array($isolation, array(
            'read_uncommitted', 'read_committed', 'repeatable_read', 'serializable'
        ))) {
            throw new \InvalidArgumentException(sprintf('Unsupported isolation level %s', json_encode($isolation)));
        }
        return constant('\Doctrine\DBAL\Connection::TRANSACTION_' . strtoupper($isolation));
    }

    private function __construct()
    {
    }
}