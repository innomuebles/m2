<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;

class ViewItemList extends AbstractDataLayer
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var array
     */
    private $selector = '';

    /**
     * Set items
     *
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Set selector
     *
     * @param string $selector
     */
    public function setSelector(string $selector)
    {
        $this->selector = $selector;
    }

    /**
     * Get selector
     *
     * @return array|string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get GTM datalayer for pages with item list
     *
     * @return array
     */
    protected function getDataLayer(): array
    {
        return $this->getItems();
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $html = parent::_toHtml();
        if ($html && $this->getSelector()) {
            $script = '
                document.addEventListener("DOMContentLoaded", function () {
                    var element = document.querySelector(".' . $this->getSelector() . '");
                    var checkVisibility = function () {
                        var rect = element.getBoundingClientRect();
                        if (rect && rect.top <= window.innerHeight && rect.bottom >= 0) {
                            ' . $this->stripTags($html) .'
                            window.removeEventListener("scroll", checkVisibility);
                        }
                    };
                
                    window.addEventListener("scroll", checkVisibility);
                    checkVisibility(); 
                });
            ';
            return $this->mfSecureRenderer->renderTag('script', ['style' => 'display:none'], $script, false);
        }
        return $html;
    }
}
