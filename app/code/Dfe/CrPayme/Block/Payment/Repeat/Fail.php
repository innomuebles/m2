<?php
namespace Dfe\CrPayme\Block\Payment\Repeat;
class Fail extends \Magento\Framework\View\Element\Template {
	/**
	 * @var \Dfe\CrPayme\Helper\Payment
	 */
	protected $paymentHelper;

	/**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Dfe\CrPayme\Model\Session $session,
		\Dfe\CrPayme\Helper\Payment $paymentHelper,
		array $data = []
	) {
		parent::__construct(
			$context,
			$data
		);
		$this->session = $session;
		$this->paymentHelper = $paymentHelper;
	}

	/**
	 * @return string|false
	 */
	function getPaymentUrl()
	{
		$orderId = $this->session->getLastOrderId();
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