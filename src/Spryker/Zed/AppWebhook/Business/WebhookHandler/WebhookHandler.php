<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\WebhookHandler;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\AppWebhook\Business\Identifier\IdentifierBuilderInterface;
use Spryker\Zed\AppWebhook\Persistence\AppWebhookEntityManagerInterface;
use Throwable;

class WebhookHandler
{
    /**
     * @param array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface> $webhookHandlerPlugins
     */
    public function __construct(
        protected array $webhookHandlerPlugins,
        protected AppWebhookEntityManagerInterface $appWebhookEntityManager,
        protected IdentifierBuilderInterface $identifierBuilder
    ) {
    }

    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        // get the default identifier which will be only used when the webhook is not a retry
        $identifier = $this->identifierBuilder->getIdentifier();

        // Only persist the webhook if it's not a retry.
        if ($webhookRequestTransfer->getIsRetry() !== true) {
            $webhookRequestTransfer->setIdentifier($identifier);
            $this->appWebhookEntityManager->saveWebhookRequest($webhookRequestTransfer);
        }

        try {
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

            if ($webhookResponseTransfer->getIsHandled() === false) {
                $this->appWebhookEntityManager->updateWebhookRequest($webhookRequestTransfer, $webhookResponseTransfer);
            }

            // Using packages may not set or use the isHandled at all (null by default), so we need to check for null explicitly.
            if ($webhookResponseTransfer->getIsSuccessful() === true && ($webhookResponseTransfer->getIsHandled() === true || $webhookResponseTransfer->getIsHandled() === null)) {
                $this->appWebhookEntityManager->deleteWebhookRequest($webhookRequestTransfer);
            }
        } catch (Throwable $throwable) {
            $webhookResponseTransfer
                ->setIsSuccessful(false)
                ->setMessage($throwable->getMessage());

            $this->appWebhookEntityManager->updateWebhookRequest($webhookRequestTransfer, $webhookResponseTransfer);
        }

        return $webhookResponseTransfer;
    }
}
