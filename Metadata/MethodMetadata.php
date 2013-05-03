<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Metadata;

use Axsy\TransactionalBundle\Metadata\EquatableMethodMetadataInterface;
use Metadata\MethodMetadata as BaseMethodMetadata;

/**
 * Method metadata
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class MethodMetadata extends BaseMethodMetadata implements EquatableMethodMetadataInterface
{
    /**
     * @var ID of connection
     */
    public $connection;

    /**
     * @var int Transaction isolation level
     */
    public $isolation;

    /**
     * @var array<string> Class names of exceptions to be used while making the decision of the rollback
     */
    public $exceptions;

    /**
     * @var bool Rollback mode
     */
    public $rollbackOnExceptions;

    /**
     * Determines should the rollback be performed on exception
     *
     * @param mixed $name Exception instance or class name
     *
     * @return bool
     */
    public function haveToRollbackOn($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }
        if (!is_null($this->exceptions)) {
            $key = array_search($name, $this->exceptions);

            return $this->rollbackOnExceptions ? false !== $key : false === $key;
        } else {
            return $this->rollbackOnExceptions;
        }
    }

    /**
     * Serializes metadata
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserializes metadata
     *
     * @param string $str Serialized metadata string representation
     */
    public function unserialize($str)
    {
        list(
            $this->class,
            $this->name,
            $this->connection,
            $this->isolation,
            $this->exceptions,
            $this->rollbackOnExceptions
        ) = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }

    /**
     * Returns array representation of the metadata
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            $this->class,
            $this->name,
            $this->connection,
            $this->isolation,
            $this->exceptions,
            $this->rollbackOnExceptions
        );
    }

    /**
     * {@inheritDoc}
     */
    public function equalTo(MethodMetadata $metadata)
    {
        return array_slice($this->toArray(),2) == array_slice($metadata->toArray(),2);
    }
}