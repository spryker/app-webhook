<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\WebhookHandler;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

class WebhookHandler
{
    /**
     * @param array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface> $webhookHandlerPlugins
     */
    public function __construct(protected array $webhookHandlerPlugins)
    {
    }

    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        foreach ($this->webhookHandlerPlugins as $webhookHandlerPlugin) {
            if (!$webhookHandlerPlugin->canHandle($webhookRequestTransfer)) {
                continue;
            }

            $webhookResponseTransfer = $webhookHandlerPlugin->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
        }

        if ($webhookResponseTransfer->getIsSuccessful() === null) {
            $webhookResponseTransfer
                ->setIsSuccessful(false)
                ->setMessage(sprintf('The webhook was not handled by any of the registered plugins. WebhookRequestTransfer: %s', json_encode($webhookRequestTransfer->toArray())));
        }

        return $webhookResponseTransfer;
    }
}
