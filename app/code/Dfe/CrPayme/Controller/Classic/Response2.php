<?php
namespace Dfe\CrPayme\Controller\Classic;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
# 2020-12-09, 2025-09-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Alignet_Paymecheckout` module": https://github.com/innomuebles/m2/issues/10
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Response2 extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	 /**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;


	/**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $_orderFactory;


	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Dfe\CrPayme\Model\Session $session
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Model\Session $session,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	) {
		parent::__construct($context);
		$this->session = $session;
		$this->resultPageFactory = $resultPageFactory;
		$this->_orderFactory = $orderFactory;
		$this->orderRepository = $orderRepository;
	}

	/**
	 */
	function execute():void {
		df_redirect_to_home();
	}

	function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
	{
		return null;
	}

	function validateForCsrf(RequestInterface $request): ?bool
	{
		return true;
	}
}