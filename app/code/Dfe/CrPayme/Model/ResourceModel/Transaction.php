<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Transaction extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $date;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $date
     * @param string|null $resourcePrefix
     */
    function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct(
            $context,
            $resourcePrefix
        );
        $this->date = $date;
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    function getLastPayuplOrderIdByOrderId($orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                ['txn_id']
            )->where('order_id = ?', $orderId)
            ->where('txn_type = ?', 'order')
            ->order('transaction_id ' . \Zend_Db_Select::SQL_DESC)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            return $row['txn_id'];
        }
        return false;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return bool
     */
    function checkIfNewestByPayuplOrderId($paymecheckoutOrderId)
    {
        $transactionTableName = $this->_resources->getTableName('sales_payment_transaction');
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $transactionTableName],
                ['transaction_id']
            )->joinLeft(
                ['t2' => $transactionTableName],
                't2.order_id = main_table.order_id AND t2.transaction_id > main_table.transaction_id',
                ['newer_id' => 't2.transaction_id']
            )->where('main_table.txn_id = ?', $paymecheckoutOrderId)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row && is_null($row['newer_id'])) {
            return true;
        }
        return false;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return int|false
     */
    function getOrderIdByPayuplOrderId($paymecheckoutOrderId)
    {
        return $this->getOneFieldByAnother('order_id', 'txn_id', $paymecheckoutOrderId);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return string|false
     */
    function getStatusByPayuplOrderId($paymecheckoutOrderId)
    {
        return $this->getAdditionalDataByPayuplOrderId($paymecheckoutOrderId, 'status');
    }

    /**
     * @param int $orderId
     * @return int
     */
    function getLastTryByOrderId($orderId)
    {
        return $this->getLastAdditionalDataFieldByOrderId($orderId, 'try', 0);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return string|false
     */
    function getExtOrderIdByPayuplOrderId($paymecheckoutOrderId)
    {
        return $this->getAdditionalDataByPayuplOrderId($paymecheckoutOrderId, 'order_id');
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return int|false
     */
    function getIdByPayuplOrderId($paymecheckoutOrderId)
    {
        return $this->getOneFieldByAnother('transaction_id', 'txn_id', $paymecheckoutOrderId);
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    function getLastStatusByOrderId($orderId)
    {
        return $this->getLastAdditionalDataFieldByOrderId($orderId, 'status', false);
    }

    protected function _construct()
    {

    }

    /**
     * @param string $getFieldName
     * @param string $byFieldName
     * @param mixed $value
     * @return mixed|false
     */
    protected function getOneFieldByAnother($getFieldName, $byFieldName, $value)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                [$getFieldName]
            )->where($byFieldName . ' = ?', $value)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            return $row[$getFieldName];
        }
        return false;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $field
     * @return mixed
     */
    protected function getAdditionalDataByPayuplOrderId($paymecheckoutOrderId, $field)
    {
        $serializedAdditionalInformation = $this->getOneFieldByAnother(
            'additional_information',
            'txn_id',
            $paymecheckoutOrderId
        );
        if ($serializedAdditionalInformation) {
            $additionalInformation = unserialize($serializedAdditionalInformation);
            return $additionalInformation[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS][$field];
        }
        return false;
    }

    /**
     * @param $orderId
     * @param $field
     * @param $valueIfNotFound
     * @return mixed
     */
    protected function getLastAdditionalDataFieldByOrderId($orderId, $field, $valueIfNotFound)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(
                ['main_table' => $this->_resources->getTableName('sales_payment_transaction')],
                ['additional_information']
            )->where('order_id = ?', $orderId)
            ->where('txn_type = ?', 'order')
            ->order('transaction_id ' . \Zend_Db_Select::SQL_DESC)
            ->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row) {
            $additionalInformation = unserialize($row['additional_information']);
            return $additionalInformation[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS][$field];
        }
        return $valueIfNotFound;
    }
}
