<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\WebhookHandler;

use Generated\Shared\Transfer\SpyWebhookInboxEntityTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AppWebhook\AppWebhookConfig;
use Spryker\Zed\AppWebhook\Business\Exception\AllowedNumberOfRetriesExceededException;
use Spryker\Zed\AppWebhook\Business\Identifier\IdentifierBuilderInterface;
use Spryker\Zed\AppWebhook\Business\MessageBuilder\MessageBuilder;
use Spryker\Zed\AppWebhook\Persistence\AppWebhookEntityManagerInterface;
use Spryker\Zed\AppWebhook\Persistence\AppWebhookRepositoryInterface;
use Throwable;

class WebhookHandler
{
    use LoggerTrait;

    /**
     * @param array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface> $webhookHandlerPlugins
     */
    public function __construct(
        protected array $webhookHandlerPlugins,
        protected AppWebhookConfig $appWebhookConfig,
        protected AppWebhookRepositoryInterface $appWebhookRepository,
        protected AppWebhookEntityManagerInterface $appWebhookEntityManager,
        protected IdentifierBuilderInterface $identifierBuilder
    ) {
    }

    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        // Only persist the webhook if it's not a retry.
        if ($webhookRequestTransfer->getIsRetry() !== true) {
            $webhookRequestTransfer->setIdentifier($this->identifierBuilder->getIdentifier());
            $this->appWebhookEntityManager->saveWebhookRequest($webhookRequestTransfer);
        }

        // Delete the webhook when the number of retries is exceeded and throw an exception.
        if ($webhookRequestTransfer->getIsRetry() === true && $webhookRequestTransfer->getRetries() >= $this->appWebhookConfig->getAllowedNumberOfWebhookRetries()) {
            $this->appWebhookEntityManager->deleteWebhookRequest($webhookRequestTransfer);
            $this->getLogger()->error(MessageBuilder::allowedNumberOfRetriesExceeded(), [
                WebhookRequestTransfer::CONTENT => $webhookRequestTransfer->getContent(),
            ]);

            throw new AllowedNumberOfRetriesExceededException(MessageBuilder::allowedNumberOfRetriesExceeded());
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
                    ->setMessage(MessageBuilder::webhookWasNotHandledByAnyRegisteredPlugin($webhookRequestTransfer));
            }

            if ($webhookResponseTransfer->getIsHandled() === false) {
                $this->updateWebhookRequest($webhookRequestTransfer, $webhookResponseTransfer);
            }

            // Using packages may not set or use the isHandled at all (null by default), so we need to check for null explicitly.
            if ($webhookResponseTransfer->getIsSuccessful() === true && ($webhookResponseTransfer->getIsHandled() === true || $webhookResponseTransfer->getIsHandled() === null)) {
                $this->appWebhookEntityManager->deleteWebhookRequest($webhookRequestTransfer);
            }
        } catch (Throwable $throwable) {
            $webhookResponseTransfer
                ->setIsSuccessful(false)
                ->setMessage($throwable->getMessage());

            $this->updateWebhookRequest($webhookRequestTransfer, $webhookResponseTransfer);
        }

        return $webhookResponseTransfer;
    }

    protected function updateWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): void
    {
        $sequenceNumber = $this->getSequenceNumber($webhookRequestTransfer, $webhookResponseTransfer);

        // When we persist the Webhook request before the handler is processing it we have the default identifier which needs to be used to find the entity.
        $spyWebhookInboxEntityTransfer = $this->appWebhookRepository->getWebhookInboxEntityTransferByIdentifier($webhookRequestTransfer->getIdentifierOrFail());

        if ($sequenceNumber !== null && $sequenceNumber !== 0) {
            $spyWebhookInboxEntityTransfer->setSequenceNumber($sequenceNumber);
        }

        // Update the number of retries when the WebhookRequestTransfer has the isRetry flag set to true.
        if ($webhookRequestTransfer->getIsRetry() === true) {
            $spyWebhookInboxEntityTransfer->setRetries($spyWebhookInboxEntityTransfer->getRetries() + 1);
        }

        // When the WebhookResponseTransfer contains an identifier we need to update this to be able to process it later from the outside.
        // A package that handles the WebhookRequest can set its own identifier to be able to identify unprocessed requests later.
        if ($webhookResponseTransfer->getIdentifier() !== null && $webhookResponseTransfer->getIdentifier() !== '' && $webhookResponseTransfer->getIdentifier() !== '0') {
            $spyWebhookInboxEntityTransfer->setIdentifier($webhookResponseTransfer->getIdentifier());
        }

        // In an exception case, the WebhookResponseTransfer will contain an error message, and we have to persist this for later investigations.
        if ($webhookResponseTransfer->getMessage() !== null && $webhookResponseTransfer->getMessage() !== '' && $webhookResponseTransfer->getMessage() !== '0') {
            $spyWebhookInboxEntityTransfer->setMessage($webhookResponseTransfer->getMessage());
        }

        $this->appWebhookEntityManager->updateWebhookInboxEntity($spyWebhookInboxEntityTransfer);
    }

    protected function getSequenceNumber(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): ?int
    {
        // In case we have a retry we don't need to find the latest entity to get the sequence number from and keep the existing sequence number.
        // In case of an empty identifier, we cannot find any entities to get the sequence number from then we use the default one.
        if ($webhookRequestTransfer->getIsRetry() || ($webhookResponseTransfer->getIdentifier() === null || $webhookResponseTransfer->getIdentifier() === '' || $webhookResponseTransfer->getIdentifier() === '0')) {
            return null;
        }

        // Check if we have already entities for the given identifier passed from outside and if so find the latest one
        // to get the sequence number and use the next sequence number for this entity.
        $spyWebhookInboxEntityTransfer = $this->appWebhookRepository->getLastWebhookInboxEntityTransferByIdentifier($webhookResponseTransfer->getIdentifierOrFail());

        return $spyWebhookInboxEntityTransfer instanceof SpyWebhookInboxEntityTransfer ? $spyWebhookInboxEntityTransfer->getSequenceNumber() + 1 : 0;
    }
}
