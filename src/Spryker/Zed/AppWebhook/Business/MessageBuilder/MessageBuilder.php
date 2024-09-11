<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\MessageBuilder;

class MessageBuilder
{
    public static function allowedNumberOfRetriesExceeded(): string
    {
        return 'Allowed number of retries exceeded.';
    }
}
