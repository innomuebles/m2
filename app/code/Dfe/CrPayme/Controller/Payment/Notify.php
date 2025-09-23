<?php
namespace Dfe\CrPayme\Controller\Payment;
use Magento\Framework\Exception\LocalizedException;
class Notify extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Framework\App\Action\Context
	 */
	protected $context;

	/**
	 * @var \Dfe\CrPayme\Model\ClientFactory
	 */
	protected $clientFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\ForwardFactory
	 */
	protected $resultForwardFactory;

	/**
	 * @var \Dfe\CrPayme\Logger\Logger
	 */
	protected $logger;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Dfe\CrPayme\Model\ClientFactory $clientFactory
	 * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
	 * @param \Dfe\CrPayme\Logger\Logger $logger
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Model\ClientFactory $clientFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		\Dfe\CrPayme\Logger\Logger $logger
	) {
		parent::__construct($context);
		$this->context = $context;
		$this->clientFactory = $clientFactory;
		$this->resultForwardFactory = $resultForwardFactory;
		$this->logger = $logger;
	}

	function execute()
	{
		/**
		 * @var $client \Dfe\CrPayme\Model\Client
		 */
		$request = $this->context->getRequest();
		try {
			$client = $this->clientFactory->create();
			$response = $client->orderConsumeNotification($request);
			$clientOrderHelper = $client->getOrderHelper();
			if ($clientOrderHelper->canProcessNotification($response['referenceCode'])) {
				return $clientOrderHelper->processNotification(
					$response['referenceCode'],
					$response['status'],
					$response['amount']
				);
			}
		} catch (LocalizedException $e) {
			$this->logger->critical($e);
		}
		/**
		 * @var $resultForward \Magento\Framework\Controller\Result\Forward
		 */
		$resultForward = $this->resultForwardFactory->create();
		$resultForward->forward('noroute');
		return $resultForward;
	}
}