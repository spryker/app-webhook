<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\MessageBuilder;

use Generated\Shared\Transfer\WebhookRequestTransfer;

class MessageBuilder
{
    public static function allowedNumberOfRetriesExceeded(): string
    {
        return 'Allowed number of retries exceeded.';
    }

    public static function webhookWasNotHandledByAnyRegisteredPlugin(WebhookRequestTransfer $webhookRequestTransfer): string
    {
        return sprintf('The webhook was not handled by any of the registered plugins. WebhookRequestTransfer: %s', json_encode($webhookRequestTransfer->toArray()));
    }
}
