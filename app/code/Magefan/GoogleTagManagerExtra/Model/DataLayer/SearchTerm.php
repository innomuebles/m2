<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\DataLayer;

use Magefan\GoogleTagManagerExtra\Api\DataLayer\SearchTermInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;

class SearchTerm extends AbstractDataLayer implements SearchTermInterface
{
    /**
     * @var string
     */
    protected $ecommPageType = 'searchresults';

    /**
     * @inheritDoc
     */
    public function get(string $searchTerm): array
    {
        if ($searchTerm) {
            return $this->eventWrap([
                'event' => 'search',
                'search_term' =>  $searchTerm
            ]);
        }

        return [];
    }
}
