<?php
namespace Dfe\CrPayme\Model;
# 2025-09-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Alignet_Paymecheckout` module": https://github.com/innomuebles/m2/issues/10
/**
 * @method int getLastOrderId()
 * @method Session setLastOrderId(int)
 * @method array getOrderCreateData()
 * @method Session setOrderCreateData(array)
 */
class Session extends \Magento\Framework\Session\SessionManager
{

}