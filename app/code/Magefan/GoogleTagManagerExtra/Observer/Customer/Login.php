<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Observer\Customer;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magefan\GoogleTagManagerExtra\Api\DataLayer\LoginInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;

class Login implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var LoginInterface
     */
    private $customer;

    /**
     * @var SessionManagerInterface|mixed
     */
    private $sessionManager;

    /**
     * Login constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param LoginInterface $customer
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        LoginInterface $customer,
        SessionManagerInterface $sessionManager
    ) {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Set datalayer after add customer login
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $customer = $observer->getEvent()->getCustomer();
            if (!$customer || !$customer->getId()) {
                return;
            }
            $this->sessionManager->push(
                $this->customerSession,
                $this->customer->get($customer)
            );
        }
    }
}
