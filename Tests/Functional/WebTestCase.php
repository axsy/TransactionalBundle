<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class WebTestCase extends BaseWebTestCase
{
    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir() . '/axsy_transactional');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir() . '/axsy_transactional');
    }

    protected static function createKernel(array $options = array())
    {
        return new AppKernel(isset($options['config']) ? $options['config'] : __DIR__ . '/config/default.yml');
    }
}