<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request\Colombia\CreditCard;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Model\Session;
use Magento\Framework\Model\Context;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\Colombia\CreditCard\PaymentDataBuilder;
use DigitalHub\Ebanx\Observer\Colombia\CreditCard\DataAssignObserver;
use DigitalHub\Ebanx\Model\CreditCard\Token;

class PaymentDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(){
        Bootstrap::create(BP, $_SERVER)->createApplication(Http::class);
    }
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $storeId = 1;
        $type = 'creditcard';
        $amountTotal = 123.45;
        $useSavedCc = null;
        $installments = 2;

        $additionalData = [
            DataAssignObserver::USE_SAVED_CC => $useSavedCc,
            DataAssignObserver::TOKEN => '09374032974039274032794',
            DataAssignObserver::CVV => '123',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::INSTALLMENTS => $installments
        ];

        $card = new \Ebanx\Benjamin\Models\Card([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'cvv' => $additionalData[DataAssignObserver::CVV],
            'type' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
        ]);

        $expectation = [
            'type' => $type,
            'amountTotal' => $amountTotal,
            'card' => $card,
            'instalments' => $installments,
        ];

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)->getMock();
        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $tokenModelMock = $this->getMockBuilder(Token::class)->disableOriginalConstructor()->getMock();

        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->once())
            ->method('getGrandTotalAmount')
            ->willReturn($amountTotal);

        $orderMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $ebanxHelperMock->expects($this->any())
            ->method('calculateTotalWithInterest')
            ->willReturn($amountTotal);

        $request = new PaymentDataBuilder($ebanxHelperMock, $contextMock, $loggerMock, $sessionMock, $tokenModelMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }

    public function testBuildWithToken()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $storeId = 1;
        $customerId = 1;
        $type = 'creditcard';
        $amountTotal = 123.45;
        $useSavedCc = 1;
        $installments = 2;
        $token = '09374032974039274032794';
        $payment_type_code = 'visa';

        $additionalData = [
            DataAssignObserver::USE_SAVED_CC => $useSavedCc,
            DataAssignObserver::TOKEN => $token,
            DataAssignObserver::PAYMENT_TYPE_CODE => $payment_type_code,
            DataAssignObserver::INSTALLMENTS => $installments
        ];

        $fakeTokenData = [
            'token' => $token,
            'payment_type_code' => $payment_type_code
        ];

        $card = new \Ebanx\Benjamin\Models\Card([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'type' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
        ]);

        $expectation = [
            'type' => $type,
            'amountTotal' => $amountTotal,
            'card' => $card,
            'instalments' => $installments,
        ];

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId'])
            ->getMock();
        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)->getMock();
        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $tokenModelMock = $this->getMockBuilder(Token::class)->disableOriginalConstructor()
            ->setMethods(['getTokenByIdAndCustomer'])
                ->getMock();

        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->once())
            ->method('getGrandTotalAmount')
            ->willReturn($amountTotal);

        $orderMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $quoteMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $ebanxHelperMock->expects($this->any())
            ->method('calculateTotalWithInterest')
            ->willReturn($amountTotal);

        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $fakeToken = new \Magento\Framework\DataObject();
        $fakeToken->setData($fakeTokenData);

        $tokenModelMock->expects($this->any())
            ->method('getTokenByIdAndCustomer')
            ->willReturn($fakeToken);

        $request = new PaymentDataBuilder($ebanxHelperMock, $contextMock, $loggerMock, $sessionMock, $tokenModelMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }

    public function testBuildWithInvalidToken()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->expectException(\Exception::class);

        $storeId = 1;
        $customerId = 1;
        $type = 'creditcard';
        $amountTotal = 123.45;
        $useSavedCc = 1;
        $installments = 2;
        $token = '09374032974039274032794';
        $payment_type_code = 'visa';

        $additionalData = [
            DataAssignObserver::USE_SAVED_CC => $useSavedCc,
            DataAssignObserver::TOKEN => $token,
            DataAssignObserver::PAYMENT_TYPE_CODE => $payment_type_code,
            DataAssignObserver::INSTALLMENTS => $installments
        ];

        $card = new \Ebanx\Benjamin\Models\Card([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'type' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
        ]);

        $expectation = [
            'type' => $type,
            'amountTotal' => $amountTotal,
            'card' => $card,
            'instalments' => $installments,
        ];

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId'])
            ->getMock();
        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)->getMock();
        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $tokenModelMock = $this->getMockBuilder(Token::class)->disableOriginalConstructor()
            ->setMethods(['getTokenByIdAndCustomer'])
                ->getMock();

        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->any())
            ->method('getGrandTotalAmount')
            ->willReturn($amountTotal);

        $orderMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $quoteMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $ebanxHelperMock->expects($this->any())
            ->method('calculateTotalWithInterest')
            ->willReturn($amountTotal);

        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $request = new PaymentDataBuilder($ebanxHelperMock, $contextMock, $loggerMock, $sessionMock, $tokenModelMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
