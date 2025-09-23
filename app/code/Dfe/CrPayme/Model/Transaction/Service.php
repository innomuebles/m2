<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Transaction;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Service
{
    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var \Dfe\CrPayme\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @param \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Dfe\CrPayme\Model\ResourceModel\Transaction $transactionResource
     */
    function __construct(
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Dfe\CrPayme\Model\ResourceModel\Transaction $transactionResource
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionResource = $transactionResource;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @param bool $close
     * @throws LocalizedException
     */
    function updateStatus($paymecheckoutOrderId, $status, $close = false)
    {
        /**
         * @var $transaction \Magento\Sales\Model\Order\Payment\Transaction
         */
        $id = $this->transactionResource->getIdByPayuplOrderId($paymecheckoutOrderId);
        if (!$id) {
            throw new LocalizedException(new Phrase('Transaction ' . $paymecheckoutOrderId . ' not found.'));
        }
        $transaction = $this->transactionRepository->get($id);
        if ($close) {
            $transaction->setIsClosed(1);
        }
        $rawDetailsInfo = $transaction->getAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS
        );
        $rawDetailsInfo['status'] = $status;
        $transaction
            ->setAdditionalInformation(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $rawDetailsInfo)
            ->save();
    }
}
