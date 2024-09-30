<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class AppWebhookConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @var int
     */
    protected const NUMBER_OF_ALLOWED_RETRIES = 9;

    public function getAllowedNumberOfWebhookRetries(): int
    {
        return static::NUMBER_OF_ALLOWED_RETRIES;
    }
}
