<?php
namespace Dfe\CrPayme\Controller\Payment;
use Magento\Framework\Exception\LocalizedException;
class Start extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Dfe\CrPayme\Model\ClientFactory
	 */
	protected $clientFactory;

	/**
	 * @var \Dfe\CrPayme\Model\Order
	 */
	protected $orderHelper;

	/**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	/**
	 * @var \Dfe\CrPayme\Logger\Logger
	 */
	protected $logger;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Dfe\CrPayme\Model\ClientFactory $clientFactory
	 * @param \Dfe\CrPayme\Model\Order $orderHelper
	 * @param \Dfe\CrPayme\Model\Session $session
	 * @param \Dfe\CrPayme\Logger\Logger $logger
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Model\ClientFactory $clientFactory,
		\Dfe\CrPayme\Model\Order $orderHelper,
		\Dfe\CrPayme\Model\Session $session,
		\Dfe\CrPayme\Logger\Logger $logger
	) {
		parent::__construct($context);
		$this->clientFactory = $clientFactory;
		$this->orderHelper = $orderHelper;
		$this->session = $session;
		$this->logger = $logger;
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	function execute() {
		/**
		 * @var $clientOrderHelper \Dfe\CrPayme\Model\Client\OrderInterface
		 * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
		 */
		$resultRedirect = $this->resultRedirectFactory->create();
		$redirectUrl = 'checkout/cart';
		$redirectParams = [];
		$orderId = $this->orderHelper->getOrderIdForPaymentStart();
		if ($orderId) {
			$order = $this->orderHelper->loadOrderById($orderId);
			if ($this->orderHelper->canStartFirstPayment($order)) {
				try {
					$client = $this->clientFactory->create();
					$clientOrderHelper = $client->getOrderHelper();
					$orderData = $clientOrderHelper->getDataForOrderCreate($order);
					$result = $client->orderCreate($orderData);
					$this->orderHelper->addNewOrderTransaction(
						$order,
						$result['orderId'],
						$result['extOrderId'],
						$clientOrderHelper->getNewStatus()
					);
					$this->orderHelper->setNewOrderStatus($order);

					$configHelper = $client->getConfigHelper();

					$this->session->setGatewayUrl($configHelper->getConfig('url'));
					$this->session->setPaymeEntorno($configHelper->getConfig('payme_entorno'));
					$this->session->setPaymeEsquema($configHelper->getConfig('payme_esquema'));
					$this->session->setWsDomain($configHelper->getConfig('wsdomain'));

					  $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, true)->save();
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, true)->save();
			$order->save();

					$redirectUrl = $result['redirectUri'];
				} catch (LocalizedException $e) {
					$this->logger->critical($e);
					$redirectUrl = 'paymecheckout/payment/end';
					$redirectParams = ['exception' => '1'];
				}
				$this->session->setLastOrderId($orderId);
			}
		}
		$resultRedirect->setPath($redirectUrl, $redirectParams);
		return $resultRedirect;
	}
}