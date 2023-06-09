<?php

namespace JMS\Payment\CoreBundle\Tests\Form\ChoosePaymentMethodTypeTest;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use JMS\Payment\CoreBundle\PluginController\PluginControllerInterface;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Tests\Functional\TestPlugin\Form\TestPluginType;
use JMS\Payment\CoreBundle\Util\Legacy;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpKernel\Kernel;

class ChoosePaymentMethodTypeTest extends TypeTestCase
{
    public function testAmountIsRequired()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('amount');
        $form = $this->createForm(['amount' => null]);
    }

    public function testCurrencyIsRequired()
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('currency');
        $form = $this->createForm(['currency' => null]);
    }

    public function testMethod()
    {
        $form = $this->createForm();
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->has('method'));
    }

    public function testMethodData()
    {
        $form = $this->createForm();

        foreach (['foo', 'bar'] as $method) {
            $this->assertTrue($form->has('data_'.$method));

            $config = $form->get('data_'.$method)->getConfig();

            $this->assertInstanceOf(
                TestPluginType::class,
                $config->getType()->getInnerType()
            );
        }
    }

    public function testMethodChoices()
    {
        if (!Legacy::formChoicesAsValues()) {
            $this->markTestSkipped();
        }

        $form = $this->createForm();

        $choices = $form->get('method')->getConfig()->getOption('choices');
        foreach (['form.label.foo' => 'foo', 'form.label.bar' => 'bar'] as $key => $value) {
            $this->assertArrayHasKey($key, $choices);
            $this->assertSame($value, $choices[$key]);
        }
    }

    public function testLegacyMethodChoices()
    {
        if (Legacy::formChoicesAsValues()) {
            $this->markTestSkipped();
        }

        $form = $this->createForm();

        $expected = ['foo' => 'form.label.foo', 'bar' => 'form.label.bar'];

        if (version_compare(Kernel::VERSION, '2.7.0', '>=')) {
            $expected = ['foo' => 0, 'bar' => 1];
        }

        $choices = $form->get('method')->getConfig()->getOption('choices');
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $choices);
            $this->assertSame($value, $choices[$key]);
        }
    }

    public function testDefaultMethod()
    {
        $form = $this->createForm(['default_method' => 'foo']);

        $this->assertEquals('foo', $form->get('method')->getConfig()->getOption('data'));
    }

    public function testAllowedMethods()
    {
        if (!Legacy::formChoicesAsValues()) {
            $this->markTestSkipped();
        }

        $form = $this->createForm(['allowed_methods' => ['bar']]);

        $choices = $form->get('method')->getConfig()->getOption('choices');
        $this->assertArrayNotHasKey('form.label.foo', $choices);

        foreach (['form.label.bar' => 'bar'] as $key => $value) {
            $this->assertArrayHasKey($key, $choices);
            $this->assertSame($value, $choices[$key]);
        }

        $this->assertTrue($form->has('data_bar'));
        $this->assertFalse($form->has('data_foo'));
    }

    public function testLegacyAllowedMethods()
    {
        if (Legacy::formChoicesAsValues()) {
            $this->markTestSkipped();
        }

        $form = $this->createForm(['allowed_methods' => ['bar']]);

        $choices = $form->get('method')->getConfig()->getOption('choices');
        $this->assertArrayNotHasKey('foo', $choices);
        foreach (['bar' => 'form.label.bar'] as $key => $value) {
            $this->assertArrayHasKey($key, $choices);
            $this->assertSame($value, $choices[$key]);
        }
    }

    public function testMethodOptions()
    {
        $form = $this->createForm(['method_options' => ['foo' => ['attr' => ['foo_attr']], 'bar' => ['attr' => ['bar_attr']]]]);

        foreach (['foo', 'bar'] as $method) {
            $choices = $form->get('data_'.$method)->getConfig()->getOption('attr');

            foreach ([$method.'_attr'] as $key => $value) {
                $this->assertArrayHasKey($key, $choices);
                $this->assertSame($value, $choices[$key]);
            }
        }
    }

    public function testChoiceOptions()
    {
        $form = $this->createForm(['choice_options' => ['expanded' => false, 'data' => 'baz']]);

        $config = $form->get('method')->getConfig();
        $this->assertFalse($config->getOption('expanded'));
        $this->assertEquals('baz', $config->getOption('data'));
    }

    private function createForm($options = [], $data = [])
    {
        $options = array_merge(['amount' => '10.42', 'currency' => 'EUR'], $options);

        $form = Legacy::supportsFormTypeClass()
            ? ChoosePaymentMethodType::class
            : 'jms_choose_payment_method'
        ;

        $form = $this->factory->create($form, null, $options);
        $form->submit($data);

        return $form;
    }

    protected function setUp(): void
    {
        $this->pluginController = $this->createMock(PluginControllerInterface::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $pluginType = new TestPluginType();

        if (Legacy::supportsFormTypeClass()) {
            $pluginTypeName = $pluginType::class;
        } else {
            $pluginTypeName = $pluginType->getBlockPrefix();
        }

        $type = new ChoosePaymentMethodType($this->pluginController, ['foo' => $pluginTypeName, 'bar' => $pluginTypeName]);

        if (Legacy::supportsFormTypeClass()) {
            $extensions = [$pluginType, $type];
        } else {
            $extensions = [$pluginType->getBlockPrefix() => $pluginType, $type->getName() => $type];
        }

        return [new PreloadedExtension($extensions, [])];
    }
}
