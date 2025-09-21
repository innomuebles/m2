<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magento\Framework\App\RequestInterface;
use Magefan\GoogleTagManagerExtra\Api\DataLayer\SearchTermInterface;

class SearchTerm extends AbstractDataLayer
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SearchTermInterface
     */
    private $searchTerm;

    /**
     * SearchTerm constructor.
     * @param Context $context
     * @param Config $config
     * @param RequestInterface $request
     * @param SearchTermInterface $searchTerm
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        RequestInterface $request,
        SearchTermInterface $searchTerm,
        array $data = []
    ) {
        $this->request = $request;
        $this->searchTerm = $searchTerm;
        parent::__construct($context, $config, $data);
    }

    /**
     * Get items
     *
     * @return string
     */
    public function getSearchTerm(): string
    {
        return (string)$this->request->getParam('q');
    }

    /**
     * Get GTM datalayer for pages with item list
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        $searchTerm = $this->getSearchTerm();
        return $this->searchTerm->get($searchTerm);
    }
}
