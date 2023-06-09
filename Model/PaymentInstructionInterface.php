<?php

namespace JMS\Payment\CoreBundle\Model;

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

interface PaymentInstructionInterface
{
    public const STATE_CLOSED = 1;
    public const STATE_INVALID = 2;
    public const STATE_NEW = 3;
    public const STATE_VALID = 4;

    public function getAmount();
    public function getApprovedAmount();
    public function getApprovingAmount();
    public function getCreditedAmount();
    public function getCreditingAmount();

    /**
     * @return CreditInterface[]
     */
    public function getCredits();

    public function getCurrency();
    public function getDepositedAmount();
    public function getDepositingAmount();

    /**
     * @return ExtendedDataInterface
     */
    public function getExtendedData();

    public function getId();

    /**
     * @return PaymentInterface[]
     */
    public function getPayments();

    public function getPaymentSystemName();

    /**
     * @return FinancialTransactionInterface|null
     */
    public function getPendingTransaction();

    public function getReversingApprovedAmount();
    public function getReversingCreditedAmount();
    public function getReversingDepositedAmount();
    public function getState();
    public function hasPendingTransaction();
    public function setApprovedAmount($amount);
    public function setApprovingAmount($amount);
    public function setCreditedAmount($amount);
    public function setCreditingAmount($amount);
    public function setDepositedAmount($amount);
    public function setDepositingAmount($amount);
    public function setReversingApprovedAmount($amount);
    public function setReversingCreditedAmount($amount);
    public function setReversingDepositedAmount($amount);
    public function setState($state);
}
