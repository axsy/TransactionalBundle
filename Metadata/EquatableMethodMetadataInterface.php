<?php

namespace Axsy\TransactionalBundle\Metadata;

interface EquatableMethodMetadataInterface
{
    public function equalTo(MethodMetadata $metadata);
}