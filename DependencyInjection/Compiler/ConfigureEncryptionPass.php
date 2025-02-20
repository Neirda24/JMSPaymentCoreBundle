<?php

namespace JMS\Payment\CoreBundle\DependencyInjection\Compiler;

use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureEncryptionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('payment.encryption.enabled')) {
            return;
        }

        $providers = [];

        foreach ($container->findTaggedServiceIds('payment.encryption') as $id => $attrs) {
            if (!isset($attrs[0]['alias'])) {
                throw new RuntimeException("Please define an alias attribute for tag 'payment.encryption' of service '$id'");
            }

            $providers[$attrs[0]['alias']] = $id;
        }

        $configuredProvider = $container->getParameter('payment.encryption');

        if (!array_key_exists($configuredProvider, $providers)) {
            throw new RuntimeException("The configured encryption provider ($configuredProvider) must match the alias of one of the services tagged with 'payment.encryption'");
        }

        $alias = $container->setAlias('payment.encryption', $providers[$configuredProvider]);

        if ($alias) {
            $alias->setPublic(true);
        }
    }
}
