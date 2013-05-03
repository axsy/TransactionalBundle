<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Metadata\Driver;

use Axsy\TransactionalBundle\Exception\LogicException;
use Axsy\TransactionalBundle\Metadata\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Connection;
use Metadata\Driver\DriverInterface;
use ReflectionMethod;

/**
 * Builds metadata representation based on Transactionable annotation
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * Transactionable annotation class
     */
    const TRANSACTIONABLE = 'Axsy\\TransactionalBundle\\Annotation\\Transactionable';

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * Constructor
     *
     * @param Reader $reader Annotation reader to be used
     * @param $connectionName Default connection name
     * @param int $isolation Default isolation level
     */
    public function __construct(Reader $reader, $connectionName, $isolation = Connection::TRANSACTION_READ_COMMITTED)
    {
        $this->reader = $reader;
        $this->connectionName = $connectionName;
        $this->isolation = $isolation;
    }

    /**
     * Reads associated @Tranasactionable annotations and build metadata representation
     *
     * This method omits all annotated parent actions, because that's the job of MetadataFactory
     * to get and merge them properly
     *
     * @param \ReflectionClass $class Reflection to be analysed
     * @return ClassMetadata|null Metadata representation
     *
     * @throws \Axsy\TransactionalBundle\Exception\LogicException in case of invalid annotation parameters
     */
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