<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 *
 * Transactionable annotation
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class Transactionable
{
    /**
     * ID of the connection
     *
     * @var string
     */
    public $connection;

    /**
     * Transaction isolation level
     *
     * @var int
     */
    public $isolation;

    /**
     * List of exceptions that don't impact the transaction
     *
     * @var array<string>
     */
    public $noRollbackFor;

    /**
     * List of exceptions that rollback the transaction
     *
     * @var array<string>
     */
    public $rollbackFor;
}