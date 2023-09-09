<?php

namespace MageBig\MbFrame\Plugin\LoginAsCustomer\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\LoginAsCustomerApi\Api\GetAuthenticationDataBySecretInterface;
use Magento\LoginAsCustomerApi\Api\SetLoggedAsCustomerAdminIdInterface;

class AuthenticateCustomerBySecret
{
    /**
     * @var GetAuthenticationDataBySecretInterface
     */
    private $getAuthenticationDataBySecret;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var SetLoggedAsCustomerAdminIdInterface
     */
    private $setLoggedAsCustomerAdminId;

    /**
     * @param GetAuthenticationDataBySecretInterface $getAuthenticationDataBySecret
     * @param Session $customerSession
     * @param SetLoggedAsCustomerAdminIdInterface|null $setLoggedAsCustomerAdminId
     */
    public function __construct(
        GetAuthenticationDataBySecretInterface $getAuthenticationDataBySecret,
        Session $customerSession,
        ?SetLoggedAsCustomerAdminIdInterface $setLoggedAsCustomerAdminId = null
    ) {
        $this->getAuthenticationDataBySecret = $getAuthenticationDataBySecret;
        $this->customerSession = $customerSession;
        $this->setLoggedAsCustomerAdminId = $setLoggedAsCustomerAdminId
            ?? ObjectManager::getInstance()->get(SetLoggedAsCustomerAdminIdInterface::class);
    }

    public function aroundExecute($subject, $proceed, string $secret): void
    {
        try {
            $authenticationData = $this->getAuthenticationDataBySecret->execute($secret);
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__('Login was not successful.'));
        }

        if ($this->customerSession->getId()) {
//            $this->customerSession->setCustomerId(null);
        }

        $result = $this->customerSession->loginById($authenticationData->getCustomerId());
        if (false === $result) {
            throw new LocalizedException(__('Login was not successful.'));
        }
        $this->customerSession->regenerateId();
        $this->setLoggedAsCustomerAdminId->execute($authenticationData->getAdminId());
    }
}
