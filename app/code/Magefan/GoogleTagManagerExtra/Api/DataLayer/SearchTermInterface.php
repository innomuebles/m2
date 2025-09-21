<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Api\DataLayer;

use Magento\Framework\Exception\NoSuchEntityException;

interface SearchTermInterface
{
    /**
     * Get GTM datalayer
     *
     * @param string $searchTerm
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(string $searchTerm): array;
}
