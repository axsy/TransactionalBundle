<?php

namespace Axsy\TransactionalBundle\Tests\Functional\TestBundle\Some;

use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Entity\Other\Entity;
use Axsy\TransactionalBundle\Annotation\Transactionable;

use Doctrine\Common\Persistence\ObjectManager;

class Service
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @Transactionable(connection="other")
     */
    public function doTransactional()
    {
        $entity = new Entity();
        $entity->setValue('some');

        $this->om->persist($entity);
        $this->om->flush();

        throw new \RuntimeException();
    }
}