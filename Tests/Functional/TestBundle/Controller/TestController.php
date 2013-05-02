<?php

namespace Axsy\TransactionalBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function testAction()
    {
        return new Response('Hello, Kernel!');
    }
}