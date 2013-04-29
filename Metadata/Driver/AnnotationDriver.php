<?php

namespace Axsy\TransactionalBundle\Resolver\Driver;

use Axsy\TransactionalBundle\Exception\LogicException;
use Axsy\TransactionalBundle\Resolver\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Metadata\Driver\DriverInterface;
use ReflectionMethod;

class AnnotationDriver implements DriverInterface
{
    const TRANSACTIONABLE_ANNOTATION = 'Axsy\\TransactionalBundle\\Annotation\\Transactionable';

    protected $reader;

    public function __construct(Reader $reader, $connectionName)
    {
        $this->reader = $reader;
        $this->connectionName = $connectionName;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->name);
        if (!(is_null($classAnnotation = $this->reader->getClassAnnotation($class, self::TRANSACTIONABLE_ANNOTATION)))) {
            if (!is_null($classAnnotation->noRollbackFor)) {
                if (!is_null($classAnnotation->rollbackFor)) {
                    throw new LogicException(
                        'Values for \'noRollbackFor\' and \'rollbackFor\' can\'t be provided at the same time');
                }
                $classMetadata->exceptions = $classAnnotation->noRollbackFor;
                $classMetadata->rollbackOnExceptions = false;
            } else {
                $classMetadata->exceptions = $classAnnotation->rollbackFor;
                $classMetadata->rollbackOnExceptions = true;
            }
            $classMetadata->connection = $classAnnotation->connection ?: $this->connectionName;
            $classMetadata->isolation = $classAnnotation->isolation ?: Connection::TRANSACTION_READ_COMMITTED;
        }

        foreach($class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            // Since MetadataFactory collects metadata for each class in hierarchy
            // we have to omit methods from parent classes
            if ($method->getDeclaringClass()->getName() != $class->name) {
                continue;
            }
            if (!is_null($methodAnnotation = $this->reader->getMethodAnnotation($method, self::TRANSACTIONABLE_ANNOTATION))
                || $classMetadata->hasGlobalParams()) {
                $methodMetadata = $classMetadata->createMethodMetadata($method->name);
                if (!is_null($methodAnnotation->noRollbackFor)) {
                    if (!is_null($methodAnnotation->rollbackFor)) {
                        throw new LogicException(
                            'Values for \'noRollbackFor\' and \'rollbackFor\' can\'t be provided at the same time');
                    }
                    $methodMetadata->exceptions = $methodAnnotation->noRollbackFor;
                    $methodMetadata->rollbackOnExceptions = false;
                } else {
                    $methodMetadata->exceptions = $methodAnnotation->rollbackFor;
                    $methodMetadata->rollbackOnExceptions = true;
                }
                if ($methodAnnotation->connection) {
                    $methodMetadata->connection = $methodAnnotation->connection;
                }
                if ($methodAnnotation->isolation) {
                    $methodMetadata->isolation = $methodAnnotation->isolation;
                }
                $classMetadata->addMethodMetadata($methodMetadata);
            }
        }

        return $classMetadata->isProxyRequired() ? $classMetadata : null;
    }
}