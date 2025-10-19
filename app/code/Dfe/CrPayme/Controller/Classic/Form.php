<?php
namespace Dfe\CrPayme\Controller\Classic;
class Form extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Dfe\CrPayme\Model\Session $session
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Model\Session $session,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->session = $session;
		$this->resultPageFactory = $resultPageFactory;
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
	 */
	function execute()
	{
		/**
		 * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
		 * @var $resultPage \Magento\Framework\View\Result\Page
		 */
		$orderCreateData = $this->session->getOrderCreateData();
		$paymeEntorno = $this->session->getPaymeEntorno();
		$paymeEsquema = $this->session->getPaymeEsquema();
		$WsDomain = $this->session->getWsDomain();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('payme_request');

		if ($orderCreateData) {

			$sqlDelete = "Delete FROM " . $tableName." Where purchaseOperationNumber = ".$orderCreateData['purchaseOperationNumber']."";
			$connection->query($sqlDelete);

			$sql = "Insert Into " . $tableName . " 
			(   purchaseOperationNumber,
				purchaseAmount,
				purchaseCurrencyCode,
				language,
				billingFirstName,
				billingLastName,
				billingEmail,
				billingAddress,
				billingZip,
				billingState,
				billingCountry,
				billingPhone,
				shippingFirstName,
				shippingLastName,
				shippingEmail,
				shippingAddress,
				shippingZip,
				shippingCity,
				shippingState,
				shippingCountry,
				shippingPhone,
				programmingLanguage,
				userCommerce,
				userCodePayme,
				descriptionProducts,
				purchaseVerification
			)
			Values (
				'".$orderCreateData['purchaseOperationNumber']."',
				'".$orderCreateData['purchaseAmount']."',
				'".$orderCreateData['purchaseCurrencyCode']."',
				'".$orderCreateData['language']."',
				'".$orderCreateData['billingFirstName']."',
				'".$orderCreateData['billingLastName']."',
				'".$orderCreateData['billingEmail']."',
				'".$orderCreateData['billingAddress']."',
				'".$orderCreateData['billingZIP']."',
				'".$orderCreateData['billingState']."',
				'".$orderCreateData['billingCountry']."',
				'".$orderCreateData['billingPhone']."',
				'".$orderCreateData['shippingFirstName']."',
				'".$orderCreateData['shippingLastName']."',
				'".$orderCreateData['shippingEmail']."',
				'".$orderCreateData['shippingAddress']."',
				'".$orderCreateData['shippingZIP']."',
				'".$orderCreateData['shippingCity']."',
				'".$orderCreateData['shippingState']."',
				'".$orderCreateData['shippingCountry']."',
				'".$orderCreateData['shippingPhone']."',
				'".$orderCreateData['programmingLanguage']."',
				'".$orderCreateData['userCommerce']."',
				'".$orderCreateData['userCodePayme']."',
				'".$orderCreateData['descriptionProducts']."',
				'".$orderCreateData['purchaseVerification']."'
			)";
			$connection->query($sql);
			$resultPage = $this->resultPageFactory->create(true, ['template' => 'Dfe_CrPayme::emptyroot.phtml']);
			$resultPage->addHandle($resultPage->getDefaultLayoutHandle());
			$resultPage->getLayout()->getBlock('paymecheckout.classic.form')->setOrderCreateData($orderCreateData);
			$resultPage->getLayout()->getBlock('paymecheckout.classic.form')->setGatewayUrl(
				$this->session->getGatewayUrl()
			);
			$resultPage->getLayout()->getBlock('paymecheckout.classic.form')->setPaymeEntorno($paymeEntorno);
			$resultPage->getLayout()->getBlock('paymecheckout.classic.form')->setPaymeEsquema($paymeEsquema);
			$resultPage->getLayout()->getBlock('paymecheckout.classic.form')->setWsDomain($WsDomain);
			return $resultPage;
		} else {
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('/');
			return $resultRedirect;
		}
	}
}