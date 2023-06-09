<?php

namespace JMS\Payment\CoreBundle\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use JMS\Payment\CoreBundle\Util\Legacy;
use JMS\Payment\CoreBundle\Validator\Luhn;
use JMS\Payment\CoreBundle\Validator\LuhnValidator;

class LuhnValidatorTest extends TestCase
{
    protected $context;
    protected $validator;

    protected function setUp(): void
    {
        $this->context = Legacy::isOldPathExecutionContext()
            ? $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            : $this->getMockBuilder(ExecutionContext::class)
        ;

        $this->context = $this->context
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->validator = new LuhnValidator();
        $this->validator->initialize($this->context);
    }

    protected function tearDown(): void
    {
        $this->context = null;
        $this->validator = null;
    }

    public function testNullIsValid()
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate(null, new Luhn());
    }

    public function testEmptyStringIsValid()
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate('', new Luhn());
    }

    /**
     * @dataProvider getValidNumbers
     */
    public function testValidNumbers($number)
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($number, new Luhn());
    }

    public function getValidNumbers()
    {
        return [['42424242424242424242'], ['378282246310005'], ['371449635398431'], ['378734493671000'], ['5610591081018250'], ['30569309025904'], ['38520000023237'], ['6011111111111117'], ['6011000990139424'], ['3530111333300000'], ['3566002020360505'], ['5555555555554444'], ['5105105105105100'], ['4111111111111111'], ['4012888888881881'], ['4222222222222'], ['5019717010103742'], ['6331101999990016']];
    }

    /**
     * @dataProvider getInvalidNumbers
     */
    public function testInvalidNumbers($number)
    {
        $constraint = new Luhn();

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message);

        $this->validator->validate($number, $constraint);
    }

    public function getInvalidNumbers()
    {
        return [['1234567812345678'], ['4222222222222222']];
    }
}
