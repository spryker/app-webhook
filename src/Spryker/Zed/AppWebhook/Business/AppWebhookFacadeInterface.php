<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business;

use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

/**
 * @method \Spryker\Zed\AppWebhook\Business\AppWebhookBusinessFactory getFactory()
 */
interface AppWebhookFacadeInterface
{
    /**
     * Specification:
     * - Handles the webhook request.
     * - Processes the WebhookRequestTransfer and returns the WebhookResponseTransfer.
     * - Executes WebhookHandlerPluginInterface plugins.
     *
     * @api
     */
    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer;

    /**
     * Specification:
     * - Process unhandled webhook requests.
     * - Loads all unprocessed webhooks from the inbox.
     * - Converts entities to transfers.
     * - Runs the WebhookHandler for all request transfers.
     *
     * @api
     */
    public function processUnprocessedWebhooks(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void;
}
