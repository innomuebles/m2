<?php
namespace Dfe\CrPayme\Plugin\Magento\Framework\Session;
use Magento\Framework\Session\SessionManagerInterface as ISessionManager;
use Magento\Framework\Session\SidResolver as Sb;
# 2020-12-09 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
final class SidResolver {
	/**
	 * 2019-10-07
	 * @see \Magento\Framework\Session\SidResolverInterface::getSid()
 	 * @see \Magento\Framework\Session\SidResolver::getSid()
	 * @used-by \Magento\Framework\Session\SessionManager::start()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param ISessionManager $m
	 * @return string
	 */
	function aroundGetSid(Sb $sb, \Closure $f, ISessionManager $m) {return
		!df_rp_has('paymecheckout/classic/response') ? $f($m) : df_request(self::P_SESSION)
	;}

	/**
	 * 2020-12-09
	 * @used-by aroundGetSid()
	 * @used-by \Dfe\CrPayme\Model\Client\Classic\Order\DataGetter::getBasicData()
	 */
	const P_SESSION = 'reserved1';
}
