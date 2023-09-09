<?php

namespace MageBig\Shopbybrand\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Theme\Block\Html\Pager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Widget\Block\BlockInterface;

class BrandList extends BrandAbstract implements BlockInterface, IdentityInterface
{
    protected $_template = 'brand/brand_list.phtml';
    protected $_cacheTag = 'BRAND_LIST';

    /**
     * Instance of pager block
     *
     * @var Pager
     */
    protected $pager;

    public function _construct()
    {
        parent::_construct();
        return $this->addDefaultData();
    }

    public function getCacheKeyInfo()
    {
        return [
            $this->_cacheTag,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            (int)$this->getRequest()->getParam('p', 1),
            $this->getTemplate(),
            $this->showPager(),
            $this->getIsFeatured()
        ];
    }

    public function addDefaultData()
    {
        $data = array_replace([
            'thumb_width'       => 200,
            'thumb_height'      => 200
        ], $this->getData());

        $this->setData($data);

        return $this;
    }

    public function getAlphabetTable()
    {
        $alphabetString = $this->getData('alphabet_table');
        if (!$alphabetString) {
            $alphabetString = $this->_copeConfig->getValue('magebig_shopbybrand/our_brands_page/alphabet_table', \Magento\Store\Model\ScopeInterface::SCOPE_STORES) ?: 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
        }
        return explode(',', $alphabetString);
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $pageSize = 0;
        $offset = 0;

        if ($this->showPager()) {
            $page = $this->getRequest()->getParam('p') ?: 1;
            $pageSize = $this->getProductsPerPage();
            $offset = ($page - 1) * $pageSize;
        }

        $collection = $this->getCollection($pageSize, $offset);

        $this->setBrandCollection($collection);

        return parent::_beforeToHtml();
    }

    public function getCollection($pageSize, $offset)
    {
        $isFeatured = $this->getData('is_featured') ? 1 : 0;
        $sortBy = $this->sortBy();
        $orderBy = $this->orderBy();
        $collection = $this->getBrands($pageSize, $offset, $sortBy, $orderBy, $isFeatured);

        if ($this->showPager()) {
            $page = $this->getRequest()->getParam('p') ?: 1;
            $collection->setCurPage($page);
        }

        return $collection;
    }

    public function sortBy() {
        if (!$this->getData('is_featured')) {
            return $this->getConfigValue('magebig_shopbybrand/our_brands_page/sort_by');
        } else {
            return $this->getConfigValue('magebig_shopbybrand/featured_brands/sort_by');
        }
    }

    public function orderBy() {
        if (!$this->getData('is_featured')) {
            return $this->getConfigValue('magebig_shopbybrand/our_brands_page/sort_order');
        } else {
            return $this->getConfigValue('magebig_shopbybrand/featured_brands/sort_order');
        }
    }

    public function getProductsPerPage()
    {
        return (int) $this->getConfigValue('magebig_shopbybrand/our_brands_page/products_per_page') ?: 6;
    }

    public function showPager()
    {
        return (bool) $this->getConfigValue('magebig_shopbybrand/our_brands_page/display_pagination');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPagerHtml()
    {
        $collection = $this->getCollection(0, 0);
        $pageSize = $this->getProductsPerPage();
        if ($this->showPager() && $collection->getSize() > $pageSize) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    'brand.list.pager'
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(false)
                    ->setShowPerPage(false)
                    ->setLimit($pageSize)
                    ->setCollection($collection);
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }

        return '';
    }
}
