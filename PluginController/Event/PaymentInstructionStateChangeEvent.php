<?php

namespace JMS\Payment\CoreBundle\PluginController\Event;

use Symfony\Contracts\EventDispatcher\Event;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;

class PaymentInstructionStateChangeEvent extends Event
{
    public function __construct(private PaymentInstructionInterface $paymentInstruction, private $oldState)
    {
    }

    /**
     * @return PaymentInstructionInterface
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    public function getOldState()
    {
        return $this->oldState;
    }

    public function getNewState()
    {
        return $this->paymentInstruction->getState();
    }
}
