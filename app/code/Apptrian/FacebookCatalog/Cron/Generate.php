<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\FacebookCatalog\Cron;

use Magento\Store\Model\ScopeInterface;

class Generate
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var \Apptrian\FacebookCatalog\Helper\Data
     */
    public $helper;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
    
    /**
     * Constructor
     *
     * @param \Apptrian\FacebookCatalog\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Apptrian\FacebookCatalog\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper      = $helper;
        $this->logger      = $logger;
    }
    
    /**
     * Cron method for executing optmization process.
     */
    public function execute()
    {
        $cronJobEnabled = $this->scopeConfig->isSetFlag(
            'apptrian_facebookcatalog/cron/enabled',
            ScopeInterface::SCOPE_STORE
        );
        
        if ($cronJobEnabled) {
            try {
                $this->helper->generate();
            } catch (\Exception $e) {
                $this->logger->debug(
                    'Facebook Catalog Cron: Product Feed generation failed.'
                );
                $this->logger->critical($e);
            }
        }
    }
}
