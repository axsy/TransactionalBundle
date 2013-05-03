<?php

namespace Axsy\TransactionalBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Axsy\TransactionalBundle\Annotation\Transactionable;
use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Entity\Main;
use Axsy\TransactionalBundle\Tests\Functional\TestBundle\Entity\Other;

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
}