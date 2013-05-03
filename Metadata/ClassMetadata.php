<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Metadata;

use Axsy\TransactionalBundle\Exception\LogicException;
use Metadata\MergeableClassMetadata as BaseClassMetadata;
use Metadata\MethodMetadata as BaseMethodMetadata;
use Metadata\MergeableInterface;

/**
 * Class metadata
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class ClassMetadata extends BaseClassMetadata
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
    public $rollbackOnExceptions = true;

    /**
     * Creates method metadata based on definition of class annotatiton
     *
     * @param string $name Method name
     *
     * @return MethodMetadata
     */
    public function createMethodMetadata($name)
    {
        $metadata = new MethodMetadata($this->name, $name);

        $metadata->connection = $this->connection;
        $metadata->isolation = $this->isolation;
        $metadata->exceptions = $this->exceptions;
        $metadata->rollbackOnExceptions = $this->rollbackOnExceptions;

        return $metadata;
    }

    /**
     * Adds method metadata to the storage
     *
     * @param \Metadata\MethodMetadata $metadata
     *
     * @throws \Axsy\TransactionalBundle\Exception\LogicException in case of annotating of unacceptable method
     */
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

    /**
     * Merges metadatas of classes (actually, the classes of the inheritance hierarchy)
     *
     * @param MergeableInterface $metadata Metadata to be merged
     *
     * @throws \Axsy\TransactionalBundle\Exception\LogicException in case of wrong metadata type given or inheritance issues
     */
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
            if ($metadata->reflection->getMethod($name)->getDeclaringClass()->name != $methodMetadata->class) {
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

    /**
     * Checks, does the class has class-level Transactionable definition
     *
     * @return bool
     */
    public function hasGlobalParams()
    {
        return !is_null($this->connection)
            || !is_null($this->isolation)
            || !is_null($this->exceptions);
    }

    /**
     * Checks, is AOP proxy necessary
     *
     * @return bool
     */
    public function isProxyRequired()
    {
        return !empty($this->methodMetadata);
    }

    /**
     * Serializes metadata
     *
     * @return string
     */
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

    /**
     * Unserializes metadata
     *
     * @param string $str Serialized metadata string representation
     */
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