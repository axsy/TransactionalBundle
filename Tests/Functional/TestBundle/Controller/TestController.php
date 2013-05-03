<?php

namespace Axsy\TransactionalBundle\Tests\Functional\TestBundle\Controller;

use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\AllowedException;
use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\NotAllowedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Axsy\TransactionalBundle\Annotation\Transactionable;
use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Entity\Main;
use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Entity\Other;
use Doctrine\DBAL\Connection;

class TestController extends Controller
{
    public function doNotPerformRollbackOnUnannotatedAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new \RuntimeException();
    }

    /**
     * @Transactionable
     */
    public function performRollbackOnDefaultAnnotationAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new \RuntimeException();
    }

    /**
     * @Transactionable(connection="other")
     */
    public function performRollbackOnAnnotationWithCustomConnectionAction()
    {
        $entity = new Other\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_other');
        $em->persist($entity);
        $em->flush();

        throw new \RuntimeException();
    }

    /**
     * @Transactionable(rollbackFor={"Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\AllowedException"})
     */
    public function performRollbackOnAnnotationWithAllowedExceptionsAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new AllowedException();
    }

    /**
     * @Transactionable(rollbackFor={"Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\AllowedException"})
     */
    public function doNotPerformRollbackOnAnnotationWithAllowedExceptionsAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new NotAllowedException();
    }

    /**
     * @Transactionable(noRollbackFor={"Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\NotAllowedException"})
     */
    public function performRollbackOnAnnotationWithNotAllowedExceptionsAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new AllowedException();
    }

    /**
     * @Transactionable(noRollbackFor={"Axsy\TransactionalBundle\Tests\Functional\TestBundle\Exceptions\NotAllowedException"})
     */
    public function doNotPerformRollbackOnAnnotationWithNotAllowedExceptionsAction()
    {
        $entity = new Main\Entity();
        $entity->setValue('something');

        $em = $this->container->get('em_default');
        $em->persist($entity);
        $em->flush();

        throw new NotAllowedException();
    }

    /**
     * @Transactionable(isolation=Connection::TRANSACTION_READ_UNCOMMITTED)
     */
    public function getIsolationLevelAction()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->container->get('em_default');

        return new Response($em->getConnection()->getTransactionIsolation());
    }
}