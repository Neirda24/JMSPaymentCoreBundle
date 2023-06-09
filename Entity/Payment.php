<?php

namespace JMS\Payment\CoreBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
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

class Payment implements PaymentInterface
{
    private $approvedAmount;
    private $approvingAmount;
    private DateTime $createdAt;
    private $creditedAmount;
    private $creditingAmount;
    private $depositedAmount;
    private $depositingAmount;
    private ?DateTime $expirationDate = null;
    private $id;

    private $reversingApprovedAmount;
    private $reversingCreditedAmount;
    private $reversingDepositedAmount;
    private $state;

    /**
     * @var ArrayCollection|FinancialTransaction[]
     */
    private ArrayCollection $transactions;

    private bool $attentionRequired;
    private bool $expired;
    private ?DateTime $updatedAt = null;

    public function __construct(private PaymentInstruction $paymentInstruction, private $targetAmount)
    {
        $this->approvedAmount = 0.0;
        $this->approvingAmount = 0.0;
        $this->createdAt = new DateTime();
        $this->creditedAmount = 0.0;
        $this->creditingAmount = 0.0;
        $this->depositedAmount = 0.0;
        $this->depositingAmount = 0.0;
        $this->reversingApprovedAmount = 0.0;
        $this->reversingCreditedAmount = 0.0;
        $this->reversingDepositedAmount = 0.0;
        $this->state = self::STATE_NEW;
        $this->transactions = new ArrayCollection();
        $this->attentionRequired = false;
        $this->expired = false;

        $this->paymentInstruction->addPayment($this);
    }

    public function addTransaction(FinancialTransaction $transaction)
    {
        $this->transactions->add($transaction);
        $transaction->setPayment($this);
    }

    public function getApprovedAmount()
    {
        return $this->approvedAmount;
    }

    /**
     * @return FinancialTransaction|null
     */
    public function getApproveTransaction()
    {
        foreach ($this->transactions as $transaction) {
            $type = $transaction->getTransactionType();

            if (FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE === $type
                || FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE_AND_DEPOSIT === $type) {
                return $transaction;
            }
        }

        return null;
    }

    public function getApprovingAmount()
    {
        return $this->approvingAmount;
    }

    public function getCreditedAmount()
    {
        return $this->creditedAmount;
    }

    public function getCreditingAmount()
    {
        return $this->creditingAmount;
    }

    public function getDepositedAmount()
    {
        return $this->depositedAmount;
    }

    public function getDepositingAmount()
    {
        return $this->depositingAmount;
    }

    /**
     * @return Collection|FinancialTransaction[]
     */
    public function getDepositTransactions()
    {
        return $this->transactions->filter(fn($transaction) => FinancialTransactionInterface::TRANSACTION_TYPE_DEPOSIT === $transaction->getTransactionType());
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function getId()
    {
        return $this->id;
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
    public function getPendingTransaction()
    {
        foreach ($this->transactions as $transaction) {
            if (FinancialTransactionInterface::STATE_PENDING === $transaction->getState()) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @return Collection|FinancialTransaction[]
     */
    public function getReverseApprovalTransactions()
    {
        return $this->transactions->filter(fn($transaction) => FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_APPROVAL === $transaction->getTransactionType());
    }

    /**
     * @return Collection|FinancialTransaction[]
     */
    public function getReverseDepositTransactions()
    {
        return $this->transactions->filter(fn($transaction) => FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_DEPOSIT === $transaction->getTransactionType());
    }

    public function getReversingApprovedAmount()
    {
        return $this->reversingApprovedAmount;
    }

    public function getReversingCreditedAmount()
    {
        return $this->reversingCreditedAmount;
    }

    public function getReversingDepositedAmount()
    {
        return $this->reversingDepositedAmount;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getTargetAmount()
    {
        return $this->targetAmount;
    }

    /**
     * @return ArrayCollection|FinancialTransaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    public function hasPendingTransaction()
    {
        return null !== $this->getPendingTransaction();
    }

    public function isAttentionRequired()
    {
        return $this->attentionRequired;
    }

    public function isExpired()
    {
        if (true === $this->expired) {
            return true;
        }

        if (null !== $this->expirationDate) {
            return $this->expirationDate->getTimestamp() < time();
        }

        return false;
    }

    public function onPreSave()
    {
        $this->updatedAt = new DateTime();
    }

    public function setApprovedAmount($amount)
    {
        $this->approvedAmount = $amount;
    }

    public function setApprovingAmount($amount)
    {
        $this->approvingAmount = $amount;
    }

    public function setAttentionRequired($boolean)
    {
        $this->attentionRequired = (bool) $boolean;
    }

    public function setCreditedAmount($amount)
    {
        $this->creditedAmount = $amount;
    }

    public function setCreditingAmount($amount)
    {
        $this->creditingAmount = $amount;
    }

    public function setDepositedAmount($amount)
    {
        $this->depositedAmount = $amount;
    }

    public function setDepositingAmount($amount)
    {
        $this->depositingAmount = $amount;
    }

    public function setExpirationDate(DateTime $date)
    {
        $this->expirationDate = $date;
    }

    public function setExpired($boolean)
    {
        $this->expired = (bool) $boolean;
    }

    public function setReversingApprovedAmount($amount)
    {
        $this->reversingApprovedAmount = $amount;
    }

    public function setReversingCreditedAmount($amount)
    {
        $this->reversingCreditedAmount = $amount;
    }

    public function setReversingDepositedAmount($amount)
    {
        $this->reversingDepositedAmount = $amount;
    }

    public function setState($state)
    {
        $this->state = $state;
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
