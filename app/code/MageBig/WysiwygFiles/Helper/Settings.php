<?php

namespace MageBig\WysiwygFiles\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Settings
 */
class Settings extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH_FILETYPES = 'wysiwyg/filetypes';

    public $configPathModule = 'cms';

    protected $_storeManager;

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        if (!$this->_storeId) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * @param $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    public function getConfigValue($path)
    {
        if (substr_count($path, '/') < 2) {
            $path = $this->configPathModule . '/' . $path;
        }
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Get extra file types
     *
     * @return array|string[]
     */
    public function getExtraFiletypes()
    {
        $filetypes = [];
        $value = $this->getConfigValue(self::CONFIG_PATH_FILETYPES);
        if ($value) {
            $settings = json_decode($this->getConfigValue(self::CONFIG_PATH_FILETYPES));
            if ($settings) {
                foreach ($settings as $setting) {
                    $filetypes[] = $setting->extension;
                }
            }
        }

        $files = ['doc', 'docm', 'docx', 'csv', 'xml', 'xls', 'xlsx', 'pdf', 'zip', 'tar', 'mp4', 'webp', 'rar', 'ogg'];
        return array_merge($filetypes, $files);
    }
}
