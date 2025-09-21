<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model;

use Magefan\GoogleTagManagerExtra\Model\SessionFactory as SessionFactory;
use Magefan\GoogleTagManagerExtra\Model\ResourceModel\Session as ResourceSession;
use Magefan\GoogleTagManagerExtra\Model\ResourceModel\Session\CollectionFactory as SessionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SessionRepository
{
    /**
     * @var ResourceSession
     */
    private $resource;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var SessionCollectionFactory
     */
    private $sessionCollectionFactory;

    /**
     * @var Session
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var array
     */
    private $sessions;

    /**
     * @param ResourceSession $resource
     * @param SessionFactory $sessionFactory
     * @param SessionCollectionFactory $sessionCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceSession $resource,
        SessionFactory $sessionFactory,
        SessionCollectionFactory $sessionCollectionFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->sessionFactory = $sessionFactory;
        $this->sessionCollectionFactory = $sessionCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param $session
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function save($session)
    {
        try {
            $this->resource->save($session);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the session: %1',
                $exception->getMessage()
            ));
        }
        return $session;
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function get($clientId)
    {
        if (isset($this->sessions[$clientId])) {
            return $this->sessions[$clientId];
        }

        $session = $this->sessionFactory->create();
        $this->resource->load($session, $clientId, 'client_id');
        $this->sessions[$clientId] = $session;

        return $session;
    }

    /**
     * @param $session
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete($session)
    {
        try {
            $sessionModel = $this->sessionFactory->create();
            $this->resource->load($sessionModel, $session->getSessionId());
            $this->resource->delete($sessionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Session: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param $sessionId
     * @return true
     * @throws CouldNotDeleteException
     */
    public function deleteById($sessionId)
    {
        return $this->delete($this->get($sessionId));
    }
}
