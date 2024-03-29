<?php
/**
 * Quote index action admin panel.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Controller\Adminhtml\Managequotes;

use Webkul\Quotesystem\Controller\Adminhtml\Managequotes as Managequotes;
use Magento\Framework\Controller\ResultFactory;

class ViewQuote extends Managequotes
{
    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $quoteId = $this->_request->getParam('quote_id');
        $this->backendSession->setQuoteId($quoteId);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_Quotesystem::quotes');
        $resultPage->getConfig()->getTitle()->prepend(
            __('Quote Manager')
        );
        return $resultPage;
    }
}
