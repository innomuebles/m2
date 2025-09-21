<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\Catalog;

use Magento\Framework\View\Element\Template;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\Registry;

class ConfigurableProduct extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->config->isEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
