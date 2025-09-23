<?php
namespace Dfe\CrPayme\Controller\Payment;
class Repeat extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Framework\App\Action\Context
	 */
	protected $context;

	/**
	 * @var \Dfe\CrPayme\Helper\Payment
	 */
	protected $paymentHelper;

	/**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Helper\Payment $paymentHelper,
		\Dfe\CrPayme\Model\Session $session
	) {
		parent::__construct($context);
		$this->context = $context;
		$this->paymentHelper = $paymentHelper;
		$this->session = $session;
	}

	function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect;
	}
}