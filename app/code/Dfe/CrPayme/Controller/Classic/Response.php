<?php
namespace Dfe\CrPayme\Controller\Classic;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
# 2020-12-09, 2025-09-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Alignet_Paymecheckout` module": https://github.com/innomuebles/m2/issues/10
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Response extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	 /**
	 * @var \Dfe\CrPayme\Model\Session
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;


	/**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $_orderFactory;


	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Dfe\CrPayme\Model\Session $session
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Dfe\CrPayme\Model\Session $session,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	) {
		parent::__construct($context);
		$this->session = $session;
		$this->resultPageFactory = $resultPageFactory;
		$this->_orderFactory = $orderFactory;
		$this->orderRepository = $orderRepository;
	}

	/**
	 */
	function execute():void {
		try {
			$res = $this->getRequest()->getPostValue(); /** @var array(string => mixed) $res */
			df_log_l($this, $res, dfa($res, 'purchaseOperationNumber'));
			$this->session->setPostdata($res);
			$authorizationResult = trim($res['authorizationResult']) == "" ? "-" : $res['authorizationResult'];
			$res['paymentReferenceCode'] = trim(isset($res['paymentReferenceCode'])) == "" ? "-" : $res['paymentReferenceCode'];
			$res['purchaseVerification'] =trim(isset($res['purchaseVerification'])) == "" ? "-" : $res['purchaseVerification'];
			$res['purchaseOperationNumber'] = str_pad($res['purchaseOperationNumber'], 8, "0", STR_PAD_LEFT);
			$res['plan'] = trim(isset($res['plan'])) == "" ? "-" : $res['plan'];
			$res['cuota'] =  trim(isset($res['cuota'])) == "" ? "-" : $res['cuota'];
			$res['montoAproxCuota'] =  trim(isset($res['montoAproxCuota'])) == "" ? "-" : $res['montoAproxCuota'];
			$res['resultadoOperacion'] =  trim(isset($res['resultadoOperacion'])) == "" ? "-" : $res['resultadoOperacion'];
			$res['paymethod'] =  trim(isset($res['paymethod'])) == "" ? "-" : $res['paymethod'];
			$res['fechaHora'] =  trim(isset($res['fechaHora'])) == "" ? "-" : $res['fechaHora'];
			$res['numeroCip'] =  trim(isset($res['numeroCip'])) == "" ? "-" : $res['numeroCip'];
			$res['brand'] =  trim(isset($res['brand'])) == "" ? "-" : $res['brand'];
			$orderId = (int) substr($res['purchaseOperationNumber'],4,6);
			if ($orderId) {
			   $o = df_order($orderId); /** @var $O o */
			}
			else {
				df_error($res['answerMessage']);
			}
			$iso_code = $res['purchaseCurrencyCode'] ;
			switch ($iso_code) {
				case '840':
					$res['purchaseCurrencyCode'] = 'USD ';
					break;
				case '604':
					$res['purchaseCurrencyCode'] = 'S/ ';
					break;
				 case '068':
					$res['purchaseCurrencyCode'] = ' BS ';
					break;
				 case '188':
					$res['purchaseCurrencyCode'] = 'CRC ';
					break;
				default:
					$res['purchaseCurrencyCode'] = 'USD';
					break;
			}
			$res['orden'] =  $orderId;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$tableName = $resource->getTableName('payme_log');
			$sqlDelete = "Delete FROM " . $tableName." Where purchaseOperationNumber = $orderId";
			$connection->query($sqlDelete);
			$sql = "Insert Into " . $tableName . " 
				( 
					id_order,
					authorizationResult,
					authorizationCode,
					errorCode,
					errorMessage,
					bin,
					brand,
					paymentReferenceCode,
					purchaseOperationNumber,
					purchaseAmount,
					purchaseCurrencyCode,
					purchaseVerification,
					plan,
					cuota,
					montoAproxCuota,
					resultadoOperacion,
					paymethod,
					fechaHora,
					reserved1,
					reserved2,
					reserved3,
					reserved4,
					reserved5,
					reserved6,
					reserved7,
					reserved8,
					reserved9,
					reserved10,
					numeroCip
				)
				Values (				
				   '".$orderId."',
				   '".$res['authorizationResult']."',
				   '".$res['authorizationCode']."',
				   '".$res['errorCode']."',
				   '".$res['errorMessage']."',
				   '".$res['bin']."',
				   '".$res['brand']."',
				   '".$res['paymentReferenceCode']."',
				   '".$res['purchaseOperationNumber']."',
				   '".$res['purchaseAmount']."',
				   '".$res['purchaseCurrencyCode']."',
				   '".$res['purchaseVerification']."',
				   '".$res['plan']."',
				   '".$res['cuota']."',
				   '".$res['montoAproxCuota']."',
				   '".$res['resultadoOperacion']."',
				   '".$res['paymethod']."',
				   '".$res['fechaHora']."',
				   '".$res['reserved1']."',
				   '".$res['reserved2']."',
				   '".$res['reserved3']."',
				   '".$res['reserved4']."',
				   '".$res['reserved5']."',
				   '".$res['reserved6']."',
				   '".$res['reserved7']."',
				   '".$res['reserved8']."',
				   '".$res['reserved9']."',
				   '".$res['reserved10']."',
				   '".$res['numeroCip']."'
				)";
			$connection->query($sql);
			if ($authorizationResult == '00') {
				$op = dfp($o); /** @var OP $op */
				dfp_action($op, IM::ACTION_AUTHORIZE_CAPTURE);
				$o->save();
				if (!df_my_local()) {
					dfp_mail($o);
				}
				$res = [
					'msgFecha' => "Este pedido fue generado el {$res['txDateTime']}, en breve recibirá un correo a {$res['shippingEmail']} con la confirmación del pago el cual debe imprimir y/o guardar"
					,'msgNumeroOP' => 'Su transacción con número de pedido '.$res['purchaseOperationNumber'].' fue autorizada con éxito.'
					,'responseMSG' => 'Transacción Autorizada'
					,'titleColor' => 'success'
				] + $res;
				/*$o->setState(O::STATE_PROCESSING)->save();
				$o->setStatus(O::STATE_PROCESSING)->save();
				$o->addStatusToHistory($o->getStatus(), 'El pedido ha sido procesado Correctamente');
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$objectManager->create('Magento\Sales\Model\OrderNotifier')->notify($o);
				$o->save();*/
			}
			elseif ($authorizationResult == '01') {
				$fechaHora = date("d/m/Y H:i:s");
				$res['msgNumeroOP'] = 'Su transacción con número de pedido '.$res['purchaseOperationNumber'].' fue Denegada.  Tener presente que esta operación NO HA GENERADO NINGUN COBRO en su tarjeta.';
				$res['responseMSG'] = 'Transacción Denegada';
				$res['titleColor'] = 'danger';
				$o->setState(O::STATE_CANCELED)->save();
				$o->setStatus(O::STATE_CANCELED)->save();
				$o->addStatusToHistory($o->getStatus(), 'El pedido ha sido procesado Correctamente');
				$o->save();
			}
			elseif ($authorizationResult == '05') {
				$res['msgFecha'] ='-';
				$res['answerMessage'] ='Transacción Cancelada';
				$res['msgNumeroOP'] = 'Su transacción con número de pedido '.$res['purchaseOperationNumber'].' fue Cancelada. Tener presente que esta operación NO HA GENERADO NINGUN COBRO en su tarjeta.';
				$res['responseMSG'] = 'Transacción Cancelada';
				$res['titleColor'] = 'danger';
				$o->setState(O::STATE_CANCELED)->save();
				$o->setStatus(O::STATE_CANCELED)->save();
				$o->addStatusToHistory($o->getStatus(), 'El pedido ha sido Cancelado ');
				$o->save();
			}
			elseif ($authorizationResult == '03') {
				if ($res['brand'] == 6 || $res['brand'] == 25) {
					$res['brand'] = "PAGO EFECTIVO";
				}
				elseif ($res['brand'] == 7 || $res['brand']== 34) {
					$res['brand'] = "SAFETYPAY";
				}
				else {
					$res['brand'] = "-";
				}
				$res['msgFecha'] = '-';
				$res['answerMessage'] ='Transacción Pendiente';
				$res['msgNumeroOP'] = 'Su transacción '.$res['purchaseOperationNumber'].' se encuentra pendiente de pago. Por favor acérquese a la agencia bancaria más cercana para realizar el pago con el siguiente código: <p class="pagoefectivo-cip">CIP: <b> '.$res['numeroCip'].'</b></p>';
				$res['responseMSG'] = 'Transacción Pendiente';
				$res['titleColor'] = 'success';
				$o->setState(O::STATE_PENDING_PAYMENT)->save();
				$o->setStatus(O::STATE_PENDING_PAYMENT)->save();
				$o->addStatusToHistory($o->getStatus(), 'El pedido ha sido procesado Correctamente');
				$o->save();
			}
			else {
				$res['msgFecha'] = '-';
				$res['responseMSG'] = 'Incompleta';
				$res['titleColor'] = 'danger';
				$res['msgNumeroOP'] = 'Su transacción con número de pedido '.$res['purchaseOperationNumber'].' fue Incompleta. Tener presente que esta operación NO HA GENERADO NINGUN COBRO en su tarjeta.';
				$o->setState(O::STATE_CANCELED)->save();
				$o->setStatus(O::STATE_CANCELED)->save();
				$o->addStatusToHistory($o->getStatus(), 'El pedido ha sido procesado Correctamente');
				$o->save();
			}
			$oid = (int)df_request_o()->getPost('purchaseOperationNumber'); /** @var int $oid */
			$o = df_order($oid); /** @var O $o */
			$cid = (int)$o->getCustomerId(); /** @var int $cid */
			if ($cid) {
				df_customer_session()->setCustomerDataAsLoggedIn(df_customer_rep()->getById($cid));
				df_customer_session()->regenerateId();
			}
			$ss = df_checkout_session(); /** @var CheckoutSession $ss */
			$ss->setLastQuoteId($o->getQuoteId());
			$ss->setLastSuccessQuoteId($o->getQuoteId());
			$ss->setLastRealOrderId($o->getIncrementId());
			$ss->setLastOrderId($o->getId());
			$ss->setLastOrderStatus($o->getStatus());
			if ('00' === $authorizationResult) {
				df_redirect_to_success();
			}
			else {
				$ss->restoreQuote();
				df_redirect_to_payment();
			}
		}
		catch (\Exception $e) {
			df_log($e);
			throw $e;
		}
	}

	function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
	{
		return null;
	}

	function validateForCsrf(RequestInterface $request): ?bool
	{
		return true;
	}
}