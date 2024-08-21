<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Orm\Zed\AppWebhook\Persistence\SpyWebhookInboxQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookPersistenceFactory getFactory()
 */
class AppWebhookRepository extends AbstractRepository implements AppWebhookRepositoryInterface
{
    /**
     * @return array<\Generated\Shared\Transfer\WebhookRequestTransfer>
     */
    public function getUnprocessedWebhookRequests(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): array
    {
        $spyWebhookInboxQuery = $this->getFactory()->createWebhookInboxQuery();
        $spyWebhookInboxQuery = $this->applyCriteria($spyWebhookInboxQuery, $webhookInboxCriteriaTransfer);

        $spyWebhookInboxEntityCollection = $spyWebhookInboxQuery->find();

        $webhookRequestTransferCollection = [];

        foreach ($spyWebhookInboxEntityCollection as $spyWebhookInboxEntity) {
            $webhookRequestTransferCollection[] = $this->getFactory()
                ->createWebhookInboxMapper()
                ->mapWebhookInboxEntityToWebhookRequestTransfer($spyWebhookInboxEntity, new WebhookRequestTransfer());
        }

        return $webhookRequestTransferCollection;
    }

    protected function applyCriteria(
        SpyWebhookInboxQuery $spyWebhookInboxQuery,
        WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer
    ): SpyWebhookInboxQuery {
        if ($webhookInboxCriteriaTransfer->getIdentifier() !== []) {
            $spyWebhookInboxQuery->filterByIdentifier_In($webhookInboxCriteriaTransfer->getIdentifier());
        }

        return $spyWebhookInboxQuery;
    }
}
