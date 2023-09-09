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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Barcode\Controller\Adminhtml\Barcode\AbstractPrint;

/**
 * Class PrintBarcode
 * @package Mageplaza\Barcode\Controller\Adminhtml\Product
 */
class PrintBarcode extends AbstractPrint
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws LocalizedException
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

        if ($data['qty'] > 200) {
            return $this->errorMessage('Quantity should be less than or equal to 200 units');
        }

        $labelData   = $this->getLabelData($data['paper_template'], $data['label_template']);
        $barcodeData = $this->getBarcodeData($data['type'], $data['paper_template'], $data['label_template']);
        $paperData   = $this->getPaperData($data['paper_template']);
        $products    = $this->_helperData->getPrintProducts([$data['id']], $this->getStoreId());

        try {
            $pdfString = $this->printBarcode(
                $data['qty'],
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
     * @param array $p
     *
     * @return mixed
     */
    public function processParameters($p)
    {
        if (isset($p['id'], $p['qty'], $p['type'], $p['label_template'], $p['paper_template'])) {
            return [
                'id'             => (int) $p['id'],
                'qty'            => (int) $p['qty'],
                'type'           => $p['type'],
                'label_template' => $p['label_template'],
                'paper_template' => $p['paper_template'],
            ];
        }

        return $this->raiseError(self::MISSING_PARAMETERS);
    }
}
