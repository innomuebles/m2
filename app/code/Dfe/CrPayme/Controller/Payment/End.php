<?php
/**
 *
 */

namespace Dfe\CrPayme\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class End extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $successValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Dfe\CrPayme\Model\Session
     */
    protected $session;

    /**
     * @var \Dfe\CrPayme\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Dfe\CrPayme\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Dfe\CrPayme\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Dfe\CrPayme\Model\Session $session
     * @param \Dfe\CrPayme\Model\ClientFactory $clientFactory
     * @param \Dfe\CrPayme\Model\Order $orderHelper
     * @param \Dfe\CrPayme\Logger\Logger $logger
     */
    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session\SuccessValidator $successValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dfe\CrPayme\Model\Session $session,
        \Dfe\CrPayme\Model\ClientFactory $clientFactory,
        \Dfe\CrPayme\Model\Order $orderHelper,
        \Dfe\CrPayme\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->successValidator = $successValidator;
        $this->checkoutSession = $checkoutSession;
        $this->session = $session;
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    function execute()
    {
        /**
         * @var $clientOrderHelper \Dfe\CrPayme\Model\Client\OrderInterface
         */



        // $resultRedirect = $this->resultRedirectFactory->create();
        // $redirectUrl = '/';
        // try {
        //     if ($this->successValidator->isValid()) {
        //         $redirectUrl = 'payme/payment/error';
        //         $this->session->setLastOrderId(null);
        //         $clientOrderHelper = $this->getClientOrderHelper();
        //         if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
        //             $redirectUrl = 'checkout/onepage/success';
        //         }

        //     } else {
        //         if ($this->session->getLastOrderId()) {
        //             $redirectUrl = 'payme/payment/repeat_error';
        //             $clientOrderHelper = $this->getClientOrderHelper();
        //             if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
        //                 $redirectUrl = 'payme/payment/repeat_success';
        //             }
        //         }
        //     }
        // } catch (LocalizedException $e) {
        //     $this->logger->critical($e);
        // }
        // $resultRedirect->setPath($redirectUrl);


        $resultPage = $this->resultPageFactory->create();
        $data = ['message' => 'Hello world!'];
        $this->session->setPostdata($data);
        $postdata = $this->session->getPostdata();
        $resultPage->getLayout()->getBlock('paymecheckout.payment.end')->setPostdata($data);
        return $resultPage;
        
        // return $resultRedirect;
    }

    /**
     * @return \Dfe\CrPayme\Model\Client\OrderInterface
     */
    protected function getClientOrderHelper()
    {
        return $this->clientFactory->create()->getOrderHelper();
    }
}