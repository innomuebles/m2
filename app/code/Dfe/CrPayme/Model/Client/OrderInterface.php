<?php
namespace Dfe\CrPayme\Model\Client;
use Magento\Framework\Exception\LocalizedException;
/**
 * 2020-12-09
 * @see \Dfe\CrPayme\Model\Client\Classic\Order
 * @see \Dfe\CrPayme\Model\Client\Rest\Order
 */
interface OrderInterface {
	/**
	 * @param array $data
	 * @return bool
	 */
	function validateCreate(array $data = []);

	/**
	 * @param $id
	 * @return bool
	 */
	function validateRetrieve($id);

	/**
	 * @param $id
	 * @return bool
	 */
	function validateCancel($id);

	/**
	 * @param array $data
	 * @return bool
	 */
	function validateStatusUpdate(array $data = []);

	/**
	 * Returns false on fail or array with the following keys on success: orderId, redirectUri, extOrderId
	 * 2020-12-09
	 * @see \Dfe\CrPayme\Model\Client\Classic\Order::create()
	 * @param array $data
	 * @return array|false
	 */
	function create(array $data);

	/**
	 * Return false on fail or array with the following keys: status, amount on success.
	 *
	 * @param string $paymecheckoutOrderId
	 * @return array|false
	 */
	function retrieve($paymecheckoutOrderId);

	/**
	 * Return false on fail or true success.
	 *
	 * @param string $paymecheckoutOrderId
	 * @return bool
	 */
	function cancel($paymecheckoutOrderId);

	/**
	 * Return false on fail or true success.
	 *
	 * @param array $data
	 * @return bool
	 */
	function statusUpdate(array $data = []);

	/**
	 * Returns false on fail or array with the following keys on success: paymecheckoutOrderId, status, amount
	 *
	 * @param \Magento\Framework\App\Request\Http $request
	 * @return array|false
	 */
	function consumeNotification(\Magento\Framework\App\Request\Http $request);

	/**
	 * 2020-12-09
	 * @see \Dfe\CrPayme\Model\Client\Classic\Order::getDataForOrderCreate()
	 * @see \Dfe\CrPayme\Model\Client\Rest\Order::getDataForOrderCreate
	 * @param \Magento\Sales\Model\Order $order
	 * @return array
	 */
	function getDataForOrderCreate(\Magento\Sales\Model\Order $order);

	/**
	 * Adds API-related special data to standard order data.
	 *
	 * @param array $data
	 * @return array
	 */
	function addSpecialDataToOrder(array $data = []);

	/**
	 * @return string
	 */
	function getNewStatus();

	/**
	 * Checks if payment was successful.
	 *
	 * @return bool
	 */
	function paymentSuccessCheck();

	/**
	 * @param string $paymecheckoutOrderId
	 * @return bool
	 */
	function canProcessNotification($paymecheckoutOrderId);

	/**
	 * @param string $paymecheckoutOrderId
	 * @param string $status
	 * @param float $amount
	 * @return \Magento\Framework\Controller\Result\Raw
	 * @throws LocalizedException
	 */
	function processNotification($paymecheckoutOrderId, $status, $amount);

	/**
	 * @param mixed $status
	 * @return string
	 */
	function getStatusDescription($status);
}
