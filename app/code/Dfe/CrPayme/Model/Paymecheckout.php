<?php
namespace Dfe\CrPayme\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class Paymecheckout  extends AbstractMethod
{
      const CODE = 'paymecheckout';

    /*
     * Path of config variables in system.xml
     */
    const XML_PATH_MERCHANT_ID               = 'payment/payme/merchantId';
    const XML_PATH_ACCOUNT_ID           = 'payment/payme/accountId';
    const XML_PATH_API_KEY              = 'payment/payme/ApiKey';
    const XML_PATH_API_LOGIN       = 'payment/payme/ApiLogin';
    const XML_PATH_TEST       = 'payment/payme/test';
     const XML_PATH_PAYME_DEBUG      = 'payment/payme_gateway/main_parameters/payme_debug';
    const XML_PATH_PAYME_ENVIROMENT       = 'payment/payme_environment';
    const XML_PATH_PAYME_ESQUEMA       = 'payment/payme_esquema';
    const XML_PATH_PAYME_MODAL_TYPE       = 'payment/payme_modal_type';
    const XML_PATH_PAYME_ADQUIR_ID       = 'payment/payme_gateway/pos_parameters_soles/payme_adquir_id';
    const XML_PATH_PAYME_COMERCE_ID       = 'payment/payme_gateway/pos_parameters_soles/payme_comerce_id';
    const XML_PATH_PAYME_VPOS_ID     = 'payment/payme_gateway/pos_parameters_soles/payme_vpos_id';
    const XML_PATH_PAYME_WALLET_ID       = 'payment/payme_gateway/pos_parameters_soles/payme_wallet_id';
    const XML_PATH_PAYME_WALLET_SECRET       = 'payment/payme_gateway/pos_parameters_soles/payme_wallet_secret';
    const XML_PATH_PAYME_ADQUIR_ID_DLS       = 'payment/payme_gateway/pos_parameters_dolares/payme_adquir_id_dls';
    const XML_PATH_PAYME_COMERCE_ID_DLS       = 'payment/payme_gateway/pos_parameters_dolares/payme_comerce_id_dls';
    const XML_PATH_PAYME_VPOS_ID_DLS       = 'payment/payme_gateway/pos_parameters_dolares/payme_vpos_id_dls';
    const XML_PATH_PAYME_WALLET_ID_DLS       = 'payment/payme_gateway/pos_parameters_dolares/payme_wallet_id_dls';
    const XML_PATH_PAYME_WALLET_SECRET_DLS       = 'payment/payme_gateway/pos_parameters_dolares/payme_wallet_secret_dls';





    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = false;
    protected $_stripeApi = false;

    /**
     * @var string
    */
    protected $_code = self::CODE;

    protected $_supportedCurrencyCodes = array('ARS','BRL','CLP','COP','MXN','PEN','USD');
    protected $urlBuilder;


    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );

        $this->urlBuilder = $urlBuilder;

    }


    function canUseForCurrency($currencyCode)
    {
        // if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
        //     return false;
        // }
        return true;
    }



}