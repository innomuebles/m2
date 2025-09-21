<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magefan\GoogleTagManager\Model\DataLayer;

class RemarketingDataPlugin
{
    /**
     * @param $subject
     * @param $data
     * @return mixed
     */
    public function afterEventWrap($subject, $data)
    {
        if (isset($data['ecommerce']['items'])) {
            $ecommPcat = [];
            $ecomm_prodid = [];
            $ecomm_pname = [];

            foreach ($data['ecommerce']['items'] as $item) {
                $categories = $item['category'] ?? '';
                if ($categories) {
                    foreach (explode(',', $categories) as $category) {
                        if (!in_array($category, $ecommPcat)) {
                            $ecommPcat[] = $category;
                        }
                    }
                }

                $itemId = $item['item_id'] ?? '';
                if ($itemId) {
                    $ecomm_prodid[] = $itemId;
                }

                $itemName = $item['item_name'] ?? '';
                if ($itemName) {
                    $ecomm_pname[] = $itemName;
                }
            }

            $data['google_tag_params'] = [
                'ecomm_pagetype' => $data['ecomm_pagetype'] ?? 'other',
                'ecomm_pcat' => implode(',', $ecommPcat),
                'ecomm_prodid' => implode(',', $ecomm_prodid),
                'ecomm_pname' => implode(',', $ecomm_pname),
            ];

            if (isset($data['ecommerce']['currency'])) {
                $data['google_tag_params']['ecomm_currency'] = $data['ecommerce']['currency'];
            }

            if (isset($data['items_qty'])) {
                $data['google_tag_params']['ecomm_items_qty'] = $data['items_qty'];
            }

            if (isset($data['ecommerce']['value'])) {
                $data['google_tag_params']['ecomm_totalvalue'] = $data['ecommerce']['value'];
            }
        }

        return $data;
    }
}
