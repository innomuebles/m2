<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model;

use Magento\Framework\Model\AbstractModel;

class Session extends AbstractModel
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Magefan\GoogleTagManagerExtra\Model\ResourceModel\Session::class);
    }

    /**
     * @param array $sessionData
     * @return void
     */
    public function setSessionData(array $newSessionData): void
    {
        $this->setData('session_data', array_merge($this->getSessionData(), $newSessionData));
    }

    /**
     * @return array
     */
    public function getSessionData(): array
    {
        $result = $this->getData('session_data');
        if (!$result) {
            return [];
        }

        return $result;
    }
}
