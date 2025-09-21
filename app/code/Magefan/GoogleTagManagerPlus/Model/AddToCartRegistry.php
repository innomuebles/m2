<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model;

class AddToCartRegistry
{
    /**
     * @var array|null
     */
    private $items;

    /**
     * @param $item
     * @return void
     */
    public function addItem($item): void
    {
        if (null == $this->items) {
            $this->items = [];
        }

        $this->items[] = $item;
    }

    /**
     * @return void
     */
    public function unsetItems(): void
    {
        $this->items = null;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        if (null == $this->items) {
            $this->items = [];
        }

        return $this->items;
    }
}
