<?php
namespace Ebanx\Payments\Api\Data;

interface OrderPaymentInterface
{

    /**#@+
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
    /*
     * Token.
     */
    const TOKEN = 'token';
    /*
    * payment_id
    */
    const PAYMENT_ID = 'payment_id';
    /*
     * Paymentmethod
     */
    const PAYMENT_METHOD = 'payment_method';
    /*
     * Amount
     */
    const AMOUNT = 'amount';
    /*
     * Amount
     */
    const TOTAL_REFUNDED = 'total_refunded';
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Gets the ID for the payment.
     *
     * @return int|null Entity ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Gets the Token for the payment.
     *
     * @return int|null Token.
     */
    public function getToken();

    /**
     * Sets Token.
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * Gets the PaymentId for the payment.
     *
     * @return int|null PaymentId.
     */
    public function getPaymentId();

    /**
     * Sets PaymentId.
     *
     * @param string $paymentId
     * @return $this
     */
    public function setPaymentId($paymentId);


    /**
     * Gets the Paymentmethod for the payment.
     *
     * @return int|null PaymentMethod.
     */
    public function getPaymentMethod();

    /**
     * Sets PaymentMethod.
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Gets the Amount for the payment.
     *
     * @return int|null Amount.
     */
    public function getAmount();

    /**
     * Sets Amount.
     *
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Gets the TotalRefunded for the payment.
     *
     * @return int|null TotalRefunded.
     */
    public function getTotalRefunded();

    /**
     * Sets Total Refunded.
     *
     * @param string $totalRefunded
     * @return $this
     */
    public function setTotalRefunded($totalRefunded);

    /**
     * Gets the created-at timestamp for the payment.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Sets the created-at timestamp for the payment.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Gets the updated-at timestamp for the payment.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Sets the updated-at timestamp for the payment.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp);

}