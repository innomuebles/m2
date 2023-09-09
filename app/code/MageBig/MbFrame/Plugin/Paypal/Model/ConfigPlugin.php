<?php

namespace MageBig\MbFrame\Plugin\Paypal\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Asset\Repository;

class ConfigPlugin
{
    /**
     * @var Session
     */
    protected $_checkoutSession;
    private $assetRepo;

    /**
     * @param Repository $assetRepo
     * @param Session|null $checkoutSession
     */
    public function __construct(
        Repository       $assetRepo,
        Session          $checkoutSession = null
    ) {
        $this->assetRepo = $assetRepo;
    }

    public function aroundGetExpressCheckoutShortcutImageUrl($subject, $proceed, $localeCode, $orderTotal = null, $pal = null): string
    {
        return $this->assetRepo->getUrl('MageBig_MbFrame::images/logo-center-other-options-blue-shop-pp.png');
    }
}
