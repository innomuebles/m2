<?php
namespace Dfe\CrPayme\Model\Client;
final class Rest extends \Dfe\CrPayme\Model\Client {
	/**
	 * @param Rest\Config $configHelper
	 * @param Rest\Order $orderHelper
	 */
	function __construct(
		Rest\Config $configHelper,
		Rest\Order $orderHelper
	) {
		parent::__construct(
			$configHelper,
			$orderHelper
		);
	}
}