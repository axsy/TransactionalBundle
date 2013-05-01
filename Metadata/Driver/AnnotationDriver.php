<?php

namespace Axsy\TransactionalBundle\Metadata\Driver;

use Axsy\TransactionalBundle\Exception\LogicException;
use Axsy\TransactionalBundle\Metadata\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Metadata\Driver\DriverInterface;
use ReflectionMethod;

class AnnotationDriver implements DriverInterface
{
    const TRANSACTIONABLE = 'Axsy\\TransactionalBundle\\Annotation\\Transactionable';

    protected $reader;

    public function __construct(Reader $reader, $connectionName, $isolation = Connection::TRANSACTION_READ_COMMITTED)
    {
        $this->reader = $reader;
        $this->connectionName = $connectionName;
        $this->isolation = $isolation;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->name);
        if (!(is_null($classAnnotation = $this->reader->getClassAnnotation($class, self::TRANSACTIONABLE)))) {
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
            $classMetadata->connection = $classAnnotation->connection ? : $this->connectionName;
            $classMetadata->isolation = $classAnnotation->isolation ? : $this->isolation;
        }

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            // Since MetadataFactory collects metadata for each class in hierarchy
            // we have to omit methods from parent classes
            if ($method->getDeclaringClass()->getName() != $class->name) {
                continue;
            }
            if (!is_null($methodAnnotation = $this->reader->getMethodAnnotation($method, self::TRANSACTIONABLE))
                || $classMetadata->hasGlobalParams()) {
                $methodMetadata = $classMetadata->createMethodMetadata($method->name);
                if (!is_null($methodAnnotation)) {
                    if (!is_null($methodAnnotation->noRollbackFor)) {
                        if (!is_null($methodAnnotation->rollbackFor)) {
                            throw new LogicException(
                                'Values for \'noRollbackFor\' and \'rollbackFor\' can\'t be provided at the same time');
                        }
                        $methodMetadata->exceptions = $methodAnnotation->noRollbackFor;
                        $methodMetadata->rollbackOnExceptions = false;
                    } elseif (!is_null($methodAnnotation->rollbackFor)) {
                        $methodMetadata->exceptions = $methodAnnotation->rollbackFor;
                        $methodMetadata->rollbackOnExceptions = true;
                    }

                    $methodMetadata->connection = $methodAnnotation->connection ? : $this->connectionName;
                    $methodMetadata->isolation = $methodAnnotation->isolation ? : $this->isolation;
                }

                $classMetadata->addMethodMetadata($methodMetadata);
            }
        }

        return $classMetadata->isProxyRequired() ? $classMetadata : null;
    }
}