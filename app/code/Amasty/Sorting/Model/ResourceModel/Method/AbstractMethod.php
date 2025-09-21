<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Api\MethodInterface;
use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\Elasticsearch\IsElasticSort;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class AbstractMethod extends AbstractDb implements MethodInterface
{
    /**
     * @var bool
     */
    public const ENABLED = true;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $methodCode;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var AdapterInterface|null
     */
    protected $indexConnection = null;

    /**
     * @var array
     */
    private $data;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        Context $context,
        \Magento\Framework\Escaper $escaper,
        ConfigProvider $configProvider,
        ?IsElasticSort $isElasticSort, // @deprecated
        $connectionName = null,
        $methodCode = '',
        $methodName = '',
        AbstractDb $indexResource = null,
        $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
        $this->helper = $context->getHelper();
        $this->logger = $context->getLogger();
        $this->date = $context->getDate();
        $this->methodCode = $methodCode;
        $this->methodName = $methodName;
        $this->data = $data;
        $this->escaper = $escaper;
        $this->configProvider = $configProvider;

        if ($indexResource) {
            $this->indexConnection = $indexResource->getConnection();
        }

        parent::__construct($context, $connectionName);
    }

    //@codingStandardsIgnoreStart
    protected function _construct()
    {
        // dummy
    }
    //@codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    abstract public function apply($collection, $direction);

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function applyCustomAttribute(Collection $collection, string $direction): void
    {
    }

    /**
     * @param Collection $collection
     * @return bool
     */
    protected function isMethodAlreadyApplied($collection)
    {
        return (bool) $collection->getFlag($this->getFlagName());
    }

    /**
     * @param Collection $collection
     */
    protected function markApplied($collection)
    {
        $collection->setFlag($this->getFlagName(), true);
    }

    /**
     * @return string
     */
    protected function getFlagName()
    {
        return  'sorted_by_' . $this->getMethodCode();
    }

    /**
     * @param int $storeId
     * @param array|null $entityIds
     * @return mixed
     */
    abstract public function getIndexedValues(int $storeId, ?array $entityIds = []);

    /**
     * Is sorting method enabled by config
     *
     * @return bool
     */
    public function isActive()
    {
        return !$this->helper->isMethodDisabled($this->getMethodCode());
    }

    /**
     * @return string
     */
    public function getMethodCode()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined Amasty sorting method code, add method code to di.xml');
        }
        return $this->methodCode;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined Amasty sorting method code, add method code to di.xml');
        }
        return $this->methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodLabel($store = null)
    {
        $label = $this->helper->getScopeValue($this->getMethodCode() . '/label', $store);
        if (!$label) {
            $label = __($this->getMethodName());
        }

        return $this->escaper->escapeHtml($label);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getAdditionalData($key)
    {
        $result = null;
        if (isset($this->data[$key])) {
            $result = $this->data[$key];
        }

        return $result;
    }
}
