<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Interception;

use CG\Core\ClassUtils;
use JMS\AopBundle\Aop\PointcutInterface;
use Metadata\MetadataFactory;

/**
 * Associates all methods annotated with Transactionable as matched
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
class TransactionPointcut implements PointcutInterface
{
    /**
     * @var \Metadata\MetadataFactory
     */
    protected $factory;

    /**
     * Constructor
     *
     * @param MetadataFactory $factory
     */
    public function __construct(MetadataFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    function matchesClass(\ReflectionClass $class)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    function matchesMethod(\ReflectionMethod $method)
    {
        $metadata = $this->factory->getMetadataForClass(ClassUtils::getUserClass($method->class));

        return !is_null($metadata) && isset($metadata->methodMetadata[$method->name]);
    }
}