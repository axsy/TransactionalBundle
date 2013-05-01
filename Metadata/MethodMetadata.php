<?php

namespace Axsy\TransactionalBundle\Metadata;

use Axsy\TransactionalBundle\Metadata\EquatableMethodMetadataInterface;
use Metadata\MethodMetadata as BaseMethodMetadata;

class MethodMetadata extends BaseMethodMetadata implements EquatableMethodMetadataInterface
{
    public $connection;
    public $isolation;
    public $exceptions;
    public $rollbackOnExceptions;

    public function haveToRollbackOn($name)
    {
        if (!is_null($this->exceptions)) {
            $key = array_search($name, $this->exceptions);

            return $this->rollbackOnExceptions ? false !== $key : false === $key;
        } else {
            return $this->rollbackOnExceptions;
        }
    }

    public function serialize()
    {
        return serialize($this->toArray());
    }

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

    public function equalTo(MethodMetadata $metadata)
    {
        return array_slice($this->toArray(),2) == array_slice($metadata->toArray(),2);
    }
}