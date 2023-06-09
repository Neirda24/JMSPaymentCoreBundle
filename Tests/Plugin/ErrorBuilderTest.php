<?php

namespace JMS\Payment\CoreBundle\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use JMS\Payment\CoreBundle\Plugin\Exception\InvalidPaymentInstructionException;
use JMS\Payment\CoreBundle\Plugin\ErrorBuilder;

class ErrorBuilderTest extends TestCase
{
    private ?ErrorBuilder $builder = null;

    public function testHasErrors()
    {
        $this->assertFalse($this->builder->hasErrors());

        $this->builder->addGlobalError('foo');
        $this->assertTrue($this->builder->hasErrors());
    }

    public function testGetException()
    {
        $this->builder->addDataError('foo', 'bar');
        $this->builder->addGlobalError('baz');

        $ex = $this->builder->getException();
        $this->assertInstanceOf(InvalidPaymentInstructionException::class, $ex);
        $this->assertSame(['foo' => 'bar'], $ex->getDataErrors());
        $this->assertSame(['baz'], $ex->getGlobalErrors());
    }

    protected function setUp(): void
    {
        $this->builder = new ErrorBuilder();
    }
}
