<?php

namespace JMS\Payment\CoreBundle\Tests\DependencyInjection\Configuration;

use JMS\Payment\CoreBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest
{
    use ConfigurationTestCaseTrait;

    public function testNoSecret()
    {
        $this->assertConfigurationIsValid([]);
        $this->assertConfigurationIsInvalid(['secret' => '']);

        $this->assertConfigurationEquals(
            [],
            ['encryption' => ['enabled' => false, 'provider' => 'defuse_php_encryption']]
        );
    }

    public function testSecret()
    {
        $this->assertConfigurationIsValid(['secret' => 'foo']);

        $this->assertConfigurationEquals(
            ['secret' => 'foo'],
            ['secret' => 'foo', 'encryption' => ['enabled' => true, 'secret' => 'foo', 'provider' => 'mcrypt']]
        );
    }

    public function testEncryptionDisabled()
    {
        $this->assertConfigurationIsValid([]);
        $this->assertConfigurationIsValid(['encryption' => false]);

        $this->assertConfigurationEquals(
            [],
            ['encryption' => ['enabled' => false, 'provider' => 'defuse_php_encryption']]
        );

        $this->assertConfigurationEquals(
            ['encryption' => false],
            ['encryption' => ['enabled' => false, 'provider' => 'defuse_php_encryption']]
        );

        $this->assertConfigurationEquals(
            ['encryption' => ['enabled' => false]],
            ['encryption' => ['enabled' => false, 'provider' => 'defuse_php_encryption']]
        );
    }

    public function testEncryptionEnabled()
    {
        $this->assertConfigurationIsInvalid(['encryption' => true]);

        $this->assertConfigurationIsInvalid(['encryption' => ['enabled' => true]]);

        $this->assertConfigurationIsValid(['encryption' => ['enabled' => true, 'secret' => 'foo']]);

        $this->assertConfigurationIsValid(['encryption' => ['secret' => 'foo']]);

        $this->assertConfigurationEquals(
            ['encryption' => ['secret' => 'foo']],
            ['encryption' => ['enabled' => true, 'secret' => 'foo', 'provider' => 'defuse_php_encryption']]
        );

        $this->assertConfigurationEquals(
            ['encryption' => ['enabled' => true, 'secret' => 'foo']],
            ['encryption' => ['enabled' => true, 'secret' => 'foo', 'provider' => 'defuse_php_encryption']]
        );
    }

    protected function getConfiguration()
    {
        return new Configuration('jms_payment_core');
    }

    protected function assertConfigurationEquals($config, $expected, $breadcrumbPath = null)
    {
        $this->assertProcessedConfigurationEquals($config, $expected, $breadcrumbPath);
    }
}
