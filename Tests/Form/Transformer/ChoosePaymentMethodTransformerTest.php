<?php

namespace JMS\Payment\CoreBundle\Tests\Form\Transformer;

use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use JMS\Payment\CoreBundle\Form\Transformer\ChoosePaymentMethodTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChoosePaymentMethodTransformerTest extends TestCase
{
    public function testTransformNullData()
    {
        $this->assertNull($this->transform(null));
    }

    public function testTransformNotPaymentInstructionObject()
    {
        $this->expectException(TransformationFailedException::class);
        $this->transform(new self());
    }

    public function testTransform()
    {
        $method = 'foo';
        $data = new ExtendedData();
        $data->set('bar', 'baz');

        $transformed = $this->transform(new PaymentInstruction('10.42', 'EUR', $method, $data));

        foreach (['method' => 'foo', 'data_foo' => ['bar' => 'baz']] as $key => $value) {
            $this->assertArrayHasKey($key, $transformed);
            $this->assertSame($value, $transformed[$key]);
        }
    }

    public function testTransformPredefinedData()
    {
        $method = 'foo';
        $data = new ExtendedData();
        $data->set('bar', 'baz');

        $options = ['predefined_data' => [$method => ['bar' => 'bar_predefined']]];

        $transformed = $this->transform(new PaymentInstruction('10.42', 'EUR', $method, $data), $options);

        $this->assertArrayNotHasKey('bar', $transformed['data_foo']);
    }

    public function testReverseTransformNullData()
    {
        $this->assertNull($this->reverseTransform(null));
    }

    public function testReverseTransformNotArray()
    {
        $this->expectException(TransformationFailedException::class);
        $this->reverseTransform(new self());
    }

    public function testReverseTransformNoAmount()
    {
        $this->expectException(TransformationFailedException::class);
        $this->reverseTransform([]);
    }

    public function testReverseTransformNoCurrency()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage("The 'currency' option must be supplied to the form");
        $this->reverseTransform([], ['amount' => '10.42']);
    }

    public function testReverseTransform()
    {
        $options = ['currency' => 'EUR', 'amount' => '10.42'];

        $pi = $this->reverseTransform(['method' => 'foo'], $options);

        $this->assertInstanceOf(PaymentInstruction::class, $pi);
        $this->assertEquals('foo', $pi->getPaymentSystemName());
        $this->assertEquals('10.42', $pi->getAmount());
        $this->assertEquals('EUR', $pi->getCurrency());
    }

    public function testReverseTransformAmountClosure()
    {
        $options = ['currency' => 'EUR', 'amount' => fn() => '10.42'];

        $pi = $this->reverseTransform([], $options);

        $this->assertEquals('10.42', $pi->getAmount());
    }

    public function testReverseTransformPredefinedDataWrongType()
    {
        $this->expectException(TransformationFailedException::class);
        $options = ['currency' => 'EUR', 'amount' => '10.42', 'predefined_data' => ['foo' => new self()]];

        $pi = $this->reverseTransform(['method' => 'foo'], $options);
    }

    public function testReverseTransformPredefinedData()
    {
        $options = ['currency' => 'EUR', 'amount' => '10.42', 'predefined_data' => ['foo' => ['bar' => 'baz']]];

        $pi = $this->reverseTransform(['method' => 'foo'], $options);

        $this->assertEquals('baz', $pi->getExtendedData()->get('bar'));
    }

    private function transform($instruction, $options = [])
    {
        $transformer = new ChoosePaymentMethodTransformer();
        $transformer->setOptions($options);

        return $transformer->transform($instruction);
    }

    private function reverseTransform($data, $options = [])
    {
        $transformer = new ChoosePaymentMethodTransformer();
        $transformer->setOptions($options);

        return $transformer->reverseTransform($data);
    }
}
