<?php
namespace Alignet\Paymecheckout\Model\Client;
final class Classic extends \Alignet\Paymecheckout\Model\Client {
	/**
	 * @param Classic\Config $configHelper
	 * @param Classic\Order $orderHelper
	 */
	function __construct(
		Classic\Config $configHelper,
		Classic\Order $orderHelper
	) {
		parent::__construct(
			$configHelper,
			$orderHelper
		);
	}
}