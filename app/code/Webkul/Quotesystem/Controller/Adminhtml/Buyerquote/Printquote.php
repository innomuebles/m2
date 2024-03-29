<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Controller\Adminhtml\Buyerquote;

use Magento\Framework\App\Action\Context;

class Printquote extends \Magento\Framework\App\Action\Action
{

    /**
     * construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Webkul\Quotesystem\Model\QuotePdf $quotePdf
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Webkul\Quotesystem\Model\QuotePdf $quotePdf,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->quotePdf = $quotePdf;
        $this->logger = $logger;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $quoteId = $this->backendSession->getQuoteId();
        if ($quoteId) {
            try {
                $this->quotePdf->generatePdf($quoteId);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong.')
                );
                $this->logger->info($e->getMessage());
            }
        } else {
            $this->messageManager->addError(
                __('Invalid Quote Id.')
            );
        }
    }
}
