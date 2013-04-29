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
        return false !== array_search($name, $this->exceptions) && $this->rollbackOnExceptions;
    }

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->connection,
            $this->isolation,
            $this->exceptions,
            $this->rollbackOnExceptions
        ));
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

    public function equalTo(MethodMetadata $metadata)
    {
        return $this->serialize() === $metadata->serialize();
    }
}