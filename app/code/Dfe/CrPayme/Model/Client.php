<?php
namespace Dfe\CrPayme\Model;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
/**
 * 2020-12-09
 * @see \Dfe\CrPayme\Model\Client\Classic
 * @see \Dfe\CrPayme\Model\Client\Rest
 */
class Client {

	/**
	 * @var Client\ConfigInterface
	 */
	protected $configHelper;



	/**
	 * @var Client\OrderInterface
	 */
	protected $orderHelper;


	/**
	 * @param Client\ConfigInterface $configHelper
	 * @param Client\OrderInterface $orderHelper
	 */
	function __construct(
		Client\ConfigInterface $configHelper,
		Client\OrderInterface $orderHelper

	) {
		$this->orderHelper = $orderHelper;
		$this->configHelper = $configHelper;
		$configHelper->setConfig();
	}

	/**
	 * 2020-12-09
	 * @used-by \Dfe\CrPayme\Controller\Payment\Start::execute()
	 * @param array $data
	 * @return array (keys: orderId, redirectUri, extOrderId)
	 * @throws LocalizedException
	 */
	final function orderCreate(array $data = [])
	{
		if (!$this->orderHelper->validateCreate($data)) {
			throw new LocalizedException(new Phrase('Order request data array is invalid.'));
		}
		$data = $this->orderHelper->addSpecialDataToOrder($data);

		$result = $this->orderHelper->create($data);
		if (!$result) {
			throw new LocalizedException(new Phrase('There was a problem while processing order create request.'));
		}
		return $result;
	}

	/**
	 * @param string $paymecheckoutOrderId
	 * @return string Transaction status
	 * @throws LocalizedException
	 */
	function orderRetrieve($paymecheckoutOrderId)
	{
		if (!$this->orderHelper->validateRetrieve($paymecheckoutOrderId)) {
			throw new LocalizedException(new Phrase('ID of order to retrieve is empty.'));
		}
		$result = $this->orderHelper->retrieve($paymecheckoutOrderId);
		if (!$result) {
			throw new LocalizedException(new Phrase('There was a problem while processing order retrieve request.'));
		}
		return $result;
	}

	/**
	 * @param string $paymecheckoutOrderId
	 * @return bool|\OpenPayU_Result
	 * @throws LocalizedException
	 */
	function orderCancel($paymecheckoutOrderId)
	{
		if (!$this->orderHelper->validateCancel($paymecheckoutOrderId)) {
			throw new LocalizedException(new Phrase('ID of order to cancel is empty.'));
		}
		$result = $this->orderHelper->cancel($paymecheckoutOrderId);
		if (!$result) {
			throw new LocalizedException(new Phrase('There was a problem while processing order cancel request.'));
		}
		return $result;
	}

	/**
	 * @param array $data
	 * @return true
	 * @throws LocalizedException
	 */
	function orderStatusUpdate(array $data = [])
	{
		if (!$this->orderHelper->validateStatusUpdate($data)) {
			throw new LocalizedException(new Phrase('Order status update request data array is invalid.'));
		}
		$result = $this->orderHelper->statusUpdate($data);
		if (!$result) {
			throw new LocalizedException(
				new Phrase('There was a problem while processing order status update request.')
			);
		}
		return true;
	}

	/**
	 * @param \Magento\Framework\App\Request\Http $request
	 * @return array (keys: paymecheckoutOrderId, status, amount)
	 * @throws LocalizedException
	 */
	function orderConsumeNotification(\Magento\Framework\App\Request\Http $request)
	{
		$result = $this->orderHelper->consumeNotification($request);
		if (!$result) {
			throw new LocalizedException(new Phrase('There was a problem while consuming order notification.'));
		}
		return $result;
	}


	/**
	 * @return Client\OrderInterface
	 */
	function getOrderHelper()
	{
		return $this->orderHelper;
	}

	/**
	 * @return Client\ConfigInterface
	 */
	function getConfigHelper()
	{
		return $this->configHelper;
	}

}
