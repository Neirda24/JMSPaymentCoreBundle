<?php

namespace JMS\Payment\CoreBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use JMS\Payment\CoreBundle\JMSPaymentCoreBundle;
use JMS\Payment\CoreBundle\Tests\Functional\TestBundle\TestBundle;
use JMS\Payment\CoreBundle\Tests\Functional\TestPlugin\TestPluginBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function sys_get_temp_dir;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(private string $config)
    {
        parent::__construct('test', true);
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import("{$configDir}/{$this->config}");
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = $this->getConfigDir();

        $routes->import("{$configDir}/../routing.yml");
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
            new JMSPaymentCoreBundle(),
            new TestPluginBundle(),
            new TestBundle(),
        ];
    }

    public function getProjectDir()
    {
        return __DIR__;
    }

    public function getConfigDir()
    {
        return __DIR__.'/TestBundle/Resources/config';
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/JMSPaymentCoreBundle/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'/JMSPaymentCoreBundle/logs';
    }
}
