<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Dependency\Plugin;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

interface WebhookHandlerPluginInterface
{
    /**
     * Specification:
     * - Checks by the WebhookRequestTransfer if this plugin can handle the webhook.
     *
     * @api
     */
    public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool;

    /**
     * Specification:
     * - Handles the webhook request.
     * - Uses the WebhookRequestTransfer to process the request.
     * - Uses the WebhookResponseTransfer to return the response.
     * - Requires the `WebhookResponseTransfer.isSuccessful` to be set.
     * - Requires the `WebhookResponseTransfer.message` to be set in case of a failure response.
     *
     * @api
     */
    public function handleWebhook(
        WebhookRequestTransfer $webhookRequestTransfer,
        WebhookResponseTransfer $webhookResponseTransfer
    ): WebhookResponseTransfer;
}
