<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magefan\GoogleTagManager\Block\DataLayer;

class PurchasePlugin
{
    /**
     * @param \Magefan\GoogleTagManager\Block\DataLayer\Purchase $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(\Magefan\GoogleTagManager\Block\DataLayer\Purchase $subject, $result): string
    {
        if ($result) {
            $result .= $subject->getLayout()
                ->createBlock(\Magefan\GoogleTagManagerExtra\Block\Checkout\TrackPurchaseEvent::class)
                ->toHtml();
        }
        return $result;
    }
}
