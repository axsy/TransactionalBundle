<?php

namespace Axsy\TransactionalBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
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

    protected function createDatabaseSchema()
    {
        foreach(array('default', 'other') as $suffix) {
            $em = self::$kernel->getContainer()->get("em_$suffix");
            $tool = new SchemaTool($em);
            $metadata = $em->getMetadataFactory()->getAllMetadata();
            if (!is_null($metadata)) {
                $tool->createSchema($metadata);
            }
        }
    }

    protected static function createKernel(array $options = array())
    {
        return new AppKernel(isset($options['config']) ? $options['config'] : __DIR__ . '/config/default.yml');
    }
}