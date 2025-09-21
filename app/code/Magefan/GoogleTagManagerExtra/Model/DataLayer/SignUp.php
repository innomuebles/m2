<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\DataLayer;

use Magefan\GoogleTagManagerExtra\Api\DataLayer\SignUpInterface;

class SignUp extends Login implements SignUpInterface
{
    /**
     * @inheritDoc
     */
    public function get($customer): array
    {
        return $this->eventWrap([
            'event' => 'sign_up',
            'method' =>  $this->getMethod() ?: 'Sign Up Form',
            'customer_id' => $customer->getId()
        ]);
    }
}
