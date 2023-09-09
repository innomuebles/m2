<?php
/**
 * Webkul Quotesystem .ls
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */


namespace Webkul\Quotesystem\Controller\Buyerquote;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

class RedirectAfterLogin extends Action
{
    protected $_context;

    public function __construct(
        Context $context
    ) {
        $this->_context = $context;
        parent::__construct($context);
    }

    public function execute()
    {
        $url = $this->_redirect->getRefererUrl();
        $login_url = $this->_url->getUrl(
            'customer/account/login',
            ['referer' => base64_encode($url)]
        );
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($login_url);
        return $resultRedirect;
    }
}
