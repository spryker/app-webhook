<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookPersistenceFactory getFactory()
 */
class AppWebhookEntityManager extends AbstractEntityManager implements AppWebhookEntityManagerInterface
{
    /**
     * When saving a WebhookRequestTransfer, we persist the whole transfer as a JSON string with the identifier provided by this package.
     * The sequence number will be set to 0 here by default.
     */
    public function saveWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer): void
    {
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxEntity();
        $spyWebhookInboxEntity
            ->setIdentifier($webhookRequestTransfer->getIdentifierOrFail())
            ->setWebhook((string)json_encode($webhookRequestTransfer->toArray()));

        $spyWebhookInboxEntity->save();
    }

    /**
     * When updating the persisted WebhookRequest we need to check first for already persisted entities with the same identifier.
     * When we have already persisted entities, we need to find the latest one to get the sequence number and use the next sequence number for this entity.
     */
    public function updateWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): void
    {
        $sequenceNumber = $this->getSequenceNumber($webhookRequestTransfer, $webhookResponseTransfer);

        // When we persist the Webhook request before the handler is processing it we have the default identifier which needs to be used to find the entity.
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier($webhookRequestTransfer->getIdentifierOrFail())
            ->findOne();

        if ($spyWebhookInboxEntity === null) {
            return;
        }

        if ($sequenceNumber !== null && $sequenceNumber !== 0) {
            $spyWebhookInboxEntity->setSequenceNumber($sequenceNumber);
        }

        // Update the number of retries when the WebhookRequestTransfer has the isRetry flag set to true.
        if ($webhookRequestTransfer->getIsRetry() === true) {
            $spyWebhookInboxEntity->setRetries($spyWebhookInboxEntity->getRetries() + 1);
        }

        // When the WebhookResponseTransfer contains an identifier we need to update this to be able to process it later from the outside.
        // A package that handles the WebhookRequest can set its own identifier to be able to identify unprocessed requests later.
        if ($webhookResponseTransfer->getIdentifier() !== null && $webhookResponseTransfer->getIdentifier() !== '' && $webhookResponseTransfer->getIdentifier() !== '0') {
            $spyWebhookInboxEntity->setIdentifier($webhookResponseTransfer->getIdentifier());
        }

        // In an exception case, the WebhookResponseTransfer will contain an error message, and we have to persist this for later investigations.
        if ($webhookResponseTransfer->getMessage() !== null && $webhookResponseTransfer->getMessage() !== '' && $webhookResponseTransfer->getMessage() !== '0') {
            $spyWebhookInboxEntity->setMessage($webhookResponseTransfer->getMessage());
        }

        $spyWebhookInboxEntity->save();
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
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier($webhookResponseTransfer->getIdentifierOrFail())
            ->orderBySequenceNumber(Criteria::DESC)
            ->findOne();

        return $spyWebhookInboxEntity !== null ? $spyWebhookInboxEntity->getSequenceNumber() + 1 : 0;
    }

    public function deleteWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer): void
    {
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier($webhookRequestTransfer->getIdentifierOrFail())
            ->findOne();

        if ($spyWebhookInboxEntity === null) {
            return;
        }

        $spyWebhookInboxEntity->delete();
    }

    public function deleteWebhookRequests(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void
    {
        $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier_In($webhookInboxCriteriaTransfer->getIdentifier())
            ->find()
            ->delete();
    }
}
