<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Barcode
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barcode\Controller\Adminhtml\Product;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Barcode\Controller\Adminhtml\Barcode\AbstractPrint;

/**
 * Class MassPrint
 * @package Mageplaza\Barcode\Controller\Adminhtml\Product
 */
class MassPrint extends AbstractPrint
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $data   = $this->processParameters($params);
        if ($this->hasError) {
            return $this->errorMessage($this->errorMessage);
        }
        $countBarcodeLabel = count($params['productIds']) * $data['qtyPerItem'];
        if ($countBarcodeLabel > 300) {
            return $this->errorMessage('Print quantity should not exceed 300 barcode labels');
        }

        $labelData   = $this->getLabelData($data['paper_template'], $data['label_template']);
        $barcodeData = $this->getBarcodeData($data['type'], $data['paper_template'], $data['label_template']);
        $paperData   = $this->getPaperData($data['paper_template']);
        $productIds  = $params['productIds'];
        $products    = $this->_helperData->getPrintProducts($productIds, $this->getStoreId());

        try {
            $pdfString = $this->printBarcode(
                $data['qtyPerItem'],
                $products,
                $barcodeData,
                $paperData,
                $labelData
            );

            if ($pdfString) {
                return $this->_json->setData([
                    'data' => base64_encode($pdfString),
                ]);
            }

            return $this->errorMessage(self::ERROR_EMPTY_BARCODE);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function processParameters($params)
    {
        if (isset($params['mp_barcode'], $params['productIds'])) {
            $barcode            = $params['mp_barcode'];
            $data['qtyPerItem'] = (int) $barcode['qty'];
            $data['type']       = isset($barcode['barcode_type'])
                ? $barcode['barcode_type']
                : $this->_helperData->getBarcodeType();

            $data['label_template'] = isset($barcode['barcode_label_template'])
                ? $barcode['barcode_label_template']
                : $this->_helperData->getLabelTemplate();

            $data['paper_template'] = isset($barcode['paper_template'])
                ? $barcode['paper_template']
                : $this->_helperData->getPaperTemplate();

            return $data;
        }

        return $this->raiseError(self::MISSING_PARAMETERS);
    }
}
