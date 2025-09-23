<?php
namespace Dfe\CrPayme\Block\Checkout;
class Fail extends \Magento\Checkout\Block\Onepage\Success {
	/**
	 * @var \Dfe\CrPayme\Helper\Payment
	 */
	protected $paymentHelper;

	function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Http\Context $httpContext,
		\Dfe\CrPayme\Helper\Payment $paymentHelper,
		array $data = []
	) {
		parent::__construct(
			$context,
			$checkoutSession,
			$orderConfig,
			$httpContext,
			$data
		);
		$this->paymentHelper = $paymentHelper;
	}

	/**
	 * Gets repeat payment URL.
	 * If it's not possible, gets start new payment URL.
	 * If it's not possible, returns false.
	 *
	 * @return string|false
	 */
	function getPaymentUrl()
	{
		$orderId = $this->_checkoutSession->getLastOrderId();
		if ($orderId) {
			$repeatPaymentUrl = $this->paymentHelper->getRepeatPaymentUrl($orderId);
			if (!$repeatPaymentUrl) {
				return $this->paymentHelper->getStartPaymentUrl($orderId);
			}
			return $repeatPaymentUrl;
		}
		return false;
	}
}