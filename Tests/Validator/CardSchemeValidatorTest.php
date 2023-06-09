<?php

namespace JMS\Payment\CoreBundle\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use JMS\Payment\CoreBundle\Util\Legacy;
use JMS\Payment\CoreBundle\Validator\CardScheme;
use JMS\Payment\CoreBundle\Validator\CardSchemeValidator;

class CardSchemeValidatorTest extends TestCase
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

        $this->validator = new CardSchemeValidator();
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

        $this->validator->validate(null, new CardScheme(['schemes' => []]));
    }

    public function testEmptyStringIsValid()
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate('', new CardScheme(['schemes' => []]));
    }

    /**
     * @dataProvider getValidNumbers
     */
    public function testValidNumbers($scheme, $number)
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($number, new CardScheme(['schemes' => [$scheme]]));
    }

    public function getValidNumbers()
    {
        return [['VISA', '42424242424242424242'], ['AMEX', '378282246310005'], ['AMEX', '371449635398431'], ['AMEX', '378734493671000'], ['DINERS', '30569309025904'], ['DISCOVER', '6011111111111117'], ['DISCOVER', '6011000990139424'], ['JCB', '3530111333300000'], ['JCB', '3566002020360505'], ['MASTERCARD', '5555555555554444'], ['MASTERCARD', '5105105105105100'], ['VISA', '4111111111111111'], ['VISA', '4012888888881881'], ['VISA', '4222222222222']];
    }
}
