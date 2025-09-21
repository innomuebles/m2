<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\ResourceModel\Session;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magefan\GoogleTagManagerExtra\Model\Session::class,
            \Magefan\GoogleTagManagerExtra\Model\ResourceModel\Session::class
        );
    }
}
