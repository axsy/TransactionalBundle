<?php

namespace Axsy\TransactionalBundle;

use Axsy\TransactionalBundle\DependencyInjection\Compiler\ResolverArgumentsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Axsy\TransactionalBundle\DependencyInjection\Compiler\DefaultConnectionPass;

class AxsyTransactionalBundle extends Bundle
{
}
