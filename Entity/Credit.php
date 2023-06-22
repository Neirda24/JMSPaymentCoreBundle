<?php

namespace JMS\Payment\CoreBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Payment\CoreBundle\Model\CreditInterface;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;

/*
 * Copyright 2010 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class Credit implements CreditInterface
{
    private bool $attentionRequired;
    private DateTime $createdAt;
    private $creditedAmount;
    private $creditingAmount;
    private $id;

    private ?PaymentInterface $payment = null;

    /**
     * @var Collection<FinancialTransaction>
     */
    private Collection $transactions;

    private $reversingAmount;
    private $state;
    private ?DateTime $updatedAt = null;

    public function __construct(private PaymentInstructionInterface $paymentInstruction, private $targetAmount)
    {
        $this->attentionRequired = false;
        $this->creditedAmount = 0.0;
        $this->creditingAmount = 0.0;
        $this->transactions = new ArrayCollection();
        $this->reversingAmount = 0.0;
        $this->state = self::STATE_NEW;
        $this->createdAt = new DateTime();

        $this->paymentInstruction->addCredit($this);
    }

    public function addTransaction(FinancialTransaction $transaction): void
    {
        $this->transactions->add($transaction);
        $transaction->setCredit($this);
    }

    public function getCreditedAmount(): float
    {
        return $this->creditedAmount;
    }

    public function getCreditingAmount(): float
    {
        return $this->creditingAmount;
    }

    /**
     * @return FinancialTransaction|null
     */
    public function getCreditTransaction(): ?FinancialTransaction
    {
        foreach ($this->transactions as $transaction) {
            if (FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT === $transaction->getTransactionType()) {
                return $transaction;
            }
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return PaymentInstruction
     */
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    /**
     * @return FinancialTransaction|null
     */
    public function getPendingTransaction(): ?FinancialTransaction
    {
        foreach ($this->transactions as $transaction) {
            if (FinancialTransactionInterface::STATE_PENDING === $transaction->getState()) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @return Collection<FinancialTransaction>
     */
    public function getReverseCreditTransactions(): Collection
    {
        return $this->transactions->filter(fn($transaction) => FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT === $transaction->getTransactionType());
    }

    public function getReversingAmount(): float
    {
        return $this->reversingAmount;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getTargetAmount()
    {
        return $this->targetAmount;
    }

    /**
     * @return ArrayCollection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function isAttentionRequired(): bool
    {
        return $this->attentionRequired;
    }

    public function isIndependent(): bool
    {
        return null === $this->payment;
    }

    public function setAttentionRequired($boolean): void
    {
        $this->attentionRequired = (bool) $boolean;
    }

    public function setPayment(PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }

    public function hasPendingTransaction(): bool
    {
        return null !== $this->getPendingTransaction();
    }

    public function setCreditedAmount($amount): void
    {
        $this->creditedAmount = $amount;
    }

    public function setCreditingAmount($amount): void
    {
        $this->creditingAmount = $amount;
    }

    public function setReversingAmount($amount): void
    {
        $this->reversingAmount = $amount;
    }

    public function setState($state): void
    {
        $this->state = $state;
    }

    public function onPreSave(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
