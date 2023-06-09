<?php

namespace JMS\Payment\CoreBundle\PluginController\Event;

use Symfony\Contracts\EventDispatcher\Event;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;

class PaymentStateChangeEvent extends Event
{
    public function __construct(private PaymentInterface $payment, private $oldState)
    {
    }

    /**
     * @return PaymentInterface
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return PaymentInstructionInterface
     */
    public function getPaymentInstruction()
    {
        return $this->payment->getPaymentInstruction();
    }

    public function getOldState()
    {
        return $this->oldState;
    }

    public function getNewState()
    {
        return $this->payment->getState();
    }
}
