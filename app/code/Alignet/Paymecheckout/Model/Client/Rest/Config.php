<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Alignet\Paymecheckout\Model\Client\Rest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Alignet\Paymecheckout\Model\Client\ConfigInterface;
use Alignet\Paymecheckout\Model\Payupl;

class Config implements ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    function setConfig()
    {
        \OpenPayU_Configuration::setEnvironment('secure');
        $merchantPosId = $this->scopeConfig->getValue(Payupl::XML_PATH_POS_ID, 'store');
        if ($merchantPosId) {
            \OpenPayU_Configuration::setMerchantPosId($merchantPosId);
        } else {
            throw new LocalizedException(new Phrase('Merchant POS ID is empty.'));
        }
        $signatureKey = $this->scopeConfig->getValue(Payupl::XML_PATH_SECOND_KEY_MD5, 'store');
        if ($signatureKey) {
            \OpenPayU_Configuration::setSignatureKey($signatureKey);
        } else {
            throw new LocalizedException(new Phrase('Signature key is empty.'));
        }
        return true;
    }

    /**
     * @param string $key
     * @return array
     */
    function getConfig($key = null)
    {
        $config = [
            'merchant_pos_id' => \OpenPayU_Configuration::getMerchantPosId(),
            'signature_key' => \OpenPayU_Configuration::getSignatureKey()
        ];
        if ($key) {
            return $config[$key];
        }
        return $config;
    }
}
