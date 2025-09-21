<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Session extends AbstractDb
{
    protected $_serializableFields = ['session_data' => [[], []]];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('magefan_gtm_session', 'id');
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $object->setClientId($value);
        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setSessionId(time());
            $object->setSessionCount(1);
            $this->save($object);
            $object->setIsFirstVisit(true);
            $object->setNewSession(true);
        } else {
            $sessionData = $object->getSessionData();
            if (isset($sessionData['page_view_previous_timestamp'])
                && ($sessionData['page_view_previous_timestamp'] < time() - 1800)) {
                $object->setSessionId((string)time());
                $object->setSessionCount($object->getSessionCount() + 1);
                $this->save($object);
                $object->setNewSession(true);
            }
        }

        //if it is date,we need to convert to timestamp
        if (($sessionId = $object->getSessionId()) && !is_numeric($sessionId)) {
            $object->setSessionId(strtotime($sessionId));
        }

        return parent::_afterLoad($object);
    }
}
