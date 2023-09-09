<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\ExtendPageBuilder\Plugin\Filter;

class CMSFilter
{
    public function afterFilter($subject, $result)
    {
        $str = '<div data-content-type="html"';
        if (strpos($result, $str) === 0 ) {
            $result = substr($result, strpos($result, '>') + 1, -6);
        }

        return $result;

    }
}
