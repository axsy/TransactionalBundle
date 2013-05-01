<?php

namespace Axsy\TransactionalBundle\Metadata;

use Axsy\TransactionalBundle\Exception\LogicException;
use Metadata\MergeableClassMetadata as BaseClassMetadata;
use Metadata\MethodMetadata as BaseMethodMetadata;
use Metadata\MergeableInterface;

class ClassMetadata extends BaseClassMetadata
{
    public $connection;
    public $isolation;
    public $exceptions;
    public $rollbackOnExceptions = true;

    public function createMethodMetadata($name)
    {
        $metadata = new MethodMetadata($this->name, $name);

        $metadata->connection = $this->connection;
        $metadata->isolation = $this->isolation;
        $metadata->exceptions = $this->exceptions;
        $metadata->rollbackOnExceptions = $this->rollbackOnExceptions;

        return $metadata;
    }

    public function addMethodMetadata(BaseMethodMetadata $metadata)
    {
        // Check cases when @Transactionable isn't allowed (because of proxy classes are being generated)
        if ($metadata->reflection->isFinal()) {
            throw new LogicException(sprintf('Method %s:%s() declared as transactionable but it can\'t be final',
                $metadata->reflection->getDeclaringClass()->name, $metadata->name));
        }
        if ($metadata->reflection->isStatic()) {
            throw new LogicException(sprintf('Method %s:%s() declared as transactionable but it can\'t be static',
                $metadata->reflection->getDeclaringClass()->name, $metadata->name));
        }
        if ($metadata->reflection->isPrivate()) {
            throw new LogicException(sprintf('Method %s:%s() declared as transactionable but it can\'t be private',
                $metadata->reflection->getDeclaringClass()->name, $metadata->name));
        }

        parent::addMethodMetadata($metadata);
    }


    public function merge(MergeableInterface $metadata)
    {
        // We can merge with the same type of metadata only
        if (!($metadata instanceof ClassMetadata)) {
            throw new LogicException(sprintf('Can merge with Axsy\TransactionalBundle\Metadata\ClassMetadata only,'
                . ' an instance of %s is given', get_class($metadata)));
        }

        // Next, lets check that overriden methods don't change transactionable behavior
        foreach($this->methodMetadata as $name => $methodMetadata) {
            // If mergeable metadata doesn't contain current method metadata,
            // this means that no metadata conflicts can be occured

            if (!($metadata->reflection->hasMethod($name))) {
                continue;
            }

            // But if it does, well, probably we're dealing with child class with at least one method overriden
            if ($metadata->name != $methodMetadata->reflection->getDeclaringClass()->name) {
                // Our assumption is right, let's make sure that both methods has identical metadata declared
                // First let's check if overriden method also has metadata
                if (!isset($metadata->methodMetadata[$name])) {
                    throw new LogicException(sprintf(
                        'Overriden method %s:%s() doesn\'t repeat parent\'s transactionable annotation', $metadata->name, $name));
                }
                // And what if metadata of overriden method isn't equal to parent method
                if(!$methodMetadata->equalTo($metadata->methodMetadata[$name])) {
                    throw new LogicException(sprintf(
                        'Overriden method %s:%s() doesn\'t repeat parent\'s transactionable annotation', $metadata->name, $name));
                }
            }
        }

        parent::merge($metadata);
    }

    public function hasGlobalParams()
    {
        return !is_null($this->connection)
            || !is_null($this->isolation)
            || !is_null($this->exceptions);
    }

    public function isProxyRequired()
    {
        return !empty($this->methodMetadata);
    }

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->connection,
            $this->isolation,
            $this->exceptions,
            $this->rollbackOnExceptions
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->connection,
            $this->isolation,
            $this->exceptions,
            $this->rollbackOnExceptions
            ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

}