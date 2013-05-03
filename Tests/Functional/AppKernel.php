<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function __construct($config)
    {
        if (!is_file($config)) {
            throw new \InvalidArgumentException(sprintf('"%s" is an invalid path to the kernel configuration',
                json_encode($config)));
        }
        $this->config = $config;

        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \JMS\AopBundle\JMSAopBundle(),
            new \JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new \Axsy\TransactionalBundle\AxsyTransactionalBundle(),
            new TestBundle\TestBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    protected function getContainerClass()
    {
        return parent::getContainerClass() . md5($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/axsy_transactional/' . md5($this->config);
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/axsy_transactional/' . md5($this->config);
    }
}