<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magento;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magento\Framework\Session\SessionManager as Session;
use Magento\Framework\App\RequestInterface;

class CustomerDataPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var SessionManagerInterface|mixed
     */
    protected $sessionManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param RequestInterface $request
     * @param Session $session
     * @param SessionManagerInterface $sessionManager
     * @param Config $config
     */
    public function __construct(
        RequestInterface $request,
        Session $session,
        SessionManagerInterface $sessionManager,
        Config $config
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }

    /**
     * Transport datalayer to frontend local storage
     *
     * @param $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, array $result): array
    {

        if (!$this->config->isEnabled() || 'customer_section_load' !== $this->request->getFullActionName()) {
            return $result;
        }

        $dataLayers = $this->sessionManager->get($this->session);
        if ($dataLayers) {
            $result['mf_datalayer'] = $dataLayers;
        }

        //add only to customer section
        if ($this->session instanceof \Magento\Customer\Model\Session) {
            if (!isset($result['mf_gtm_customer_data'])) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerData = $objectManager->get(\Magefan\GoogleTagManagerPlus\Model\GetCustomerData::class)->executeSession();

                if ($customerData) {
                    $result['mf_gtm_customer_data'] = $customerData;
                }
            }
        }


        return $result;
    }
}
