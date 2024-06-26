<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\AppWebhook\Helper;

use Closure;
use Codeception\Module;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface;

class AppWebhookHelper extends Module
{
    public function createSuccessfulWebhookHandlerPlugin(?Closure $callable = null): WebhookHandlerPluginInterface
    {
        return new class ($callable) implements WebhookHandlerPluginInterface {
            public function __construct(?Closure $callable = null)
            {
            }

            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return true;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                if ($this->callable instanceof Closure) {
                    ($this->callable)($webhookRequestTransfer);
                }

                return $webhookResponseTransfer->setIsSuccessful(true);
            }
        };
    }

    public function createFailingWebhookHandlerPlugin(string $message): WebhookHandlerPluginInterface
    {
        return new class ($message) implements WebhookHandlerPluginInterface {
            public function __construct(protected string $message)
            {
            }

            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return true;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                return $webhookResponseTransfer
                    ->setIsSuccessful(false)
                    ->setMessage($this->message);
            }
        };
    }

    public function createCanNotHandleWebhookHandlerPlugin(): WebhookHandlerPluginInterface
    {
        return new class implements WebhookHandlerPluginInterface {
            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return false;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                return $webhookResponseTransfer;
            }
        };
    }
}
