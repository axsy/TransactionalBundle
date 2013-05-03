<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\Metadata;

/**
 * Interface that allows to determine the equality
 *
 * @author Aleksey Orlov <i.trancer@gmail.com>
 */
interface EquatableMethodMetadataInterface
{
    /**
     * Checks, is MethodMetadata equal to other MethodMetadata
     *
     * @param MethodMetadata $metadata
     *
     * @return boolean
     */
    public function equalTo(MethodMetadata $metadata);
}