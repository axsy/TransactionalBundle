<?php

namespace Axsy\TransactionalBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class Transactionable
{
    /**
     * @var string
     */
    public $connection;

    /**
     * @var string
     */
    public $isolation;

    /**
     * @var array<string>
     */
    public $noRollbackFor;

    /**
     * @var array<string>
     */
    public $rollbackFor;
}