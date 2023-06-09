<?php

namespace JMS\Payment\CoreBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = [])
    {
        return self::$kernel = new AppKernel(
            $options['config'] ?? 'config.yml'
        );
    }

    protected function setUp(): void
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/JMSPaymentCoreBundle/');
    }

    protected function importDatabaseSchema()
    {
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropDatabase();
            $schemaTool->createSchema($metadata);
        }
    }
}
