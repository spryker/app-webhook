<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Generated\Shared\Transfer\SpyWebhookInboxEntityTransfer;
use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
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

    public function deleteWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer): void
    {
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier($webhookRequestTransfer->getIdentifierOrFail())
            ->findOne();

        $spyWebhookInboxEntity?->delete();
    }

    public function deleteWebhookRequests(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void
    {
        $this->getFactory()->createWebhookInboxQuery()
            ->filterByIdentifier_In($webhookInboxCriteriaTransfer->getIdentifiers())
            ->find()
            ->delete();
    }

    public function updateWebhookInboxEntity(SpyWebhookInboxEntityTransfer $spyWebhookInboxEntityTransfer): void
    {
        $spyWebhookInboxEntity = $this->getFactory()->createWebhookInboxQuery()
            ->findOneByIdWebhook($spyWebhookInboxEntityTransfer->getIdWebhookOrFail());

        $spyWebhookInboxEntity
            ?->fromArray($spyWebhookInboxEntityTransfer->toArray())
            ?->save();
    }
}
