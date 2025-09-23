<?php
namespace Dfe\CrPayme\Model\Client\Classic;
# 2020-12-09
class Order implements \Dfe\CrPayme\Model\Client\OrderInterface {
	const STATUS_PRE_NEW            = 0;
	const STATUS_NEW                = 1;
	const STATUS_CANCELLED          = 2;
	const STATUS_REJECTED           = 3;
	const STATUS_PENDING            = 4;
	const STATUS_WAITING            = 5;
	const STATUS_REJECTED_CANCELLED = 7;
	const STATUS_COMPLETED          = 99;
	const STATUS_ERROR              = 888;

	/**
	 * @var string[]
	 */
	protected $statusDescription = [
		self::STATUS_PRE_NEW => 'New',
		self::STATUS_NEW => 'New',
		self::STATUS_CANCELLED => 'Cancelled',
		self::STATUS_REJECTED => 'Rejected',
		self::STATUS_PENDING => 'Pending',
		self::STATUS_WAITING => 'Waiting for acceptance',
		self::STATUS_REJECTED_CANCELLED => 'Rejected',
		self::STATUS_COMPLETED => 'Completed',
		self::STATUS_ERROR => 'Error'
	];

	/**
	 * @var Order\DataValidator
	 */
	protected $dataValidator;

	/**
	 * @var Order\DataGetter
	 */
	protected $dataGetter;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlBuilder;

	/**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Dfe\CrPayme\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var Order\Notification
	 */
	protected $notificationHelper;

	/**
	 * @var MethodCaller
	 */
	protected $methodCaller;

	/**
	 * @var \Dfe\CrPayme\Model\ResourceModel\Transaction
	 */
	protected $transactionResource;

	/**
	 * @var Order\Processor
	 */
	protected $orderProcessor;

	/**
	 * @var \Magento\Framework\Controller\Result\RawFactory
	 */
	protected $rawResultFactory;

	/**
	 * @param \Magento\Framework\View\Context $context
	 * @param Order\DataValidator $dataValidator
	 * @param Order\DataGetter $dataGetter
	 * @param \Dfe\CrPayme\Model\Session $session
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Dfe\CrPayme\Logger\Logger $logger
	 * @param Order\Notification $notificationHelper
	 * @param MethodCaller $methodCaller
	 * @param \Dfe\CrPayme\Model\ResourceModel\Transaction $transactionResource
	 * @param Order\Processor $orderProcessor
	 * @param \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
	 */
	function __construct(
		\Magento\Framework\View\Context $context,
		Order\DataValidator $dataValidator,
		Order\DataGetter $dataGetter,
		\Dfe\CrPayme\Model\Session $session,
		\Magento\Framework\App\RequestInterface $request,
		\Dfe\CrPayme\Logger\Logger $logger,
		Order\Notification $notificationHelper,
		MethodCaller $methodCaller,
		\Dfe\CrPayme\Model\ResourceModel\Transaction $transactionResource,
		Order\Processor $orderProcessor,
		\Magento\Framework\Controller\Result\RawFactory $rawResultFactory
	) {
		$this->urlBuilder = $context->getUrlBuilder();
		$this->dataValidator = $dataValidator;
		$this->dataGetter = $dataGetter;
		$this->session = $session;
		$this->request = $request;
		$this->logger = $logger;
		$this->notificationHelper = $notificationHelper;
		$this->methodCaller = $methodCaller;
		$this->transactionResource = $transactionResource;
		$this->orderProcessor = $orderProcessor;
		$this->rawResultFactory = $rawResultFactory;
	}

	/**
	 * @inheritDoc
	 */
	function validateCreate(array $data = [])
	{
		return
			$this->dataValidator->validateEmpty($data) &&
			$this->dataValidator->validateBasicData($data);
	}

	/**
	 * @inheritDoc
	 */
	function validateRetrieve($paymecheckoutOrderId)
	{
		return $this->dataValidator->validateEmpty($paymecheckoutOrderId);
	}

	/**
	 * @inheritDoc
	 */
	function validateCancel($paymecheckoutOrderId)
	{
		return $this->dataValidator->validateEmpty($paymecheckoutOrderId);
	}

	/**
	 * @inheritDoc
	 */
	function validateStatusUpdate(array $data = [])
	{
		// TODO: Implement validateStatusUpdate() method.
	}


	/**
	 * 2020-12-09
	 * @override
	 * @see \Dfe\CrPayme\Model\Client\OrderInterface::create()
	 * @used-by \Dfe\CrPayme\Model\Client::orderCreate()
	 * @param array $data
	 * @return array
	 */
	function create(array $data) {
		$this->session->setOrderCreateData($data);

		return [
			'orderId' => md5($data['purchaseOperationNumber']),
			'extOrderId' => $data['purchaseOperationNumber'],
			'redirectUri' => $this->urlBuilder->getUrl('paymecheckout/classic/form')
		];
	}

	/**
	 * @inheritDoc
	 */
	function retrieve($paymecheckoutOrderId)
	{
		$posId = $this->dataGetter->getPosId();
		$ts = $this->dataGetter->getTs();
		$sig = $this->dataGetter->getSigForOrderRetrieve([
			'pos_id' => $posId,
			'purchaseOperationNumber' => $paymecheckoutOrderId,
			'ts' => $ts
		]);
		$result = $this->methodCaller->call('orderRetrieve', [
			$posId,
			$paymecheckoutOrderId,
			$ts,
			$sig
		]);
		if ($result) {
			return [
				'status' => $result->transStatus,
				'amount' => $result->transAmount / 100
			];
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	function cancel($paymecheckoutOrderId)
	{
		// TODO: Implement cancel() method.
	}

	/**
	 * @inheritDoc
	 */
	function statusUpdate(array $data = [])
	{
		// TODO: Implement statusUpdate() method.
	}

	/**
	 * @inheritDoc
	 */
	function consumeNotification(\Magento\Framework\App\Request\Http $request)
	{
		$paymecheckoutOrderId = $this->notificationHelper->getPayuplOrderId($request);
		$orderData = $this->retrieve($paymecheckoutOrderId);
		if ($orderData) {
			return [
				'paymecheckoutOrderId' => md5($paymecheckoutOrderId),
				'status' => $orderData['status'],
				'amount' => $orderData['amount']
			];
		}
		return false;
	}

	/**
	 * 2020-12-09
	 * @override
	 * @see \Dfe\CrPayme\Model\Client\OrderInterface::getDataForOrderCreate()
	 * @used-by \Dfe\CrPayme\Controller\Payment\Start::execute()
	 */
	function getDataForOrderCreate(\Magento\Sales\Model\Order $order) {
		return $this->dataGetter->getBasicData($order);
	}

	/**
	 * @inheritDoc
	 */
	function addSpecialDataToOrder(array $data = [])
	{

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	function getNewStatus()
	{
		return Order::STATUS_PRE_NEW;
	}

	/**
	 * @inheritDoc
	 */
	function paymentSuccessCheck()
	{
		$errorCode = $this->request->getParam('error');
		if ($errorCode) {
			$extOrderId = $this->request->getParam('purchaseOperationNumber');
			$this->logger->error('Payment error ' . $errorCode . ' for transaction ' . $extOrderId . '.');
			return false;
		}
		return true;
	}

	/**
	 * @inheritDoc
	 */
	function canProcessNotification($paymecheckoutOrderId)
	{
		return !in_array(
			$this->transactionResource->getStatusByPayuplOrderId($paymecheckoutOrderId),
			[self::STATUS_COMPLETED, self::STATUS_CANCELLED]
		);
	}

	/**
	 * @inheritDoc
	 */
	function processNotification($paymecheckoutOrderId, $status, $amount)
	{
		/**
		 * @var $result \Magento\Framework\Controller\Result\Raw
		 */
		$newest = $this->transactionResource->checkIfNewestByPayuplOrderId($paymecheckoutOrderId);
		$this->orderProcessor->processStatusChange($paymecheckoutOrderId, $status, $amount, $newest);
		$result = $this->rawResultFactory->create();
		$result
			->setHttpResponseCode(200)
			->setContents('OK');
		return $result;
	}

	/**
	 * @inheritDoc
	 */
	function getStatusDescription($status)
	{
		if (isset($this->statusDescription[$status])) {
			return (string) __($this->statusDescription[$status]);
		}
		return false;
	}
}
