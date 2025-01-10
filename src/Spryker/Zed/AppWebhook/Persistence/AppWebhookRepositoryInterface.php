<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Generated\Shared\Transfer\SpyWebhookInboxEntityTransfer;
use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;

/**
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookPersistenceFactory getFactory()
 */
interface AppWebhookRepositoryInterface
{
    /**
     * @return array<\Generated\Shared\Transfer\WebhookRequestTransfer>
     */
    public function getUnprocessedWebhookRequests(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): array;

    public function getWebhookInboxEntityTransferByIdentifier(string $identifier): SpyWebhookInboxEntityTransfer;

    public function getLastWebhookInboxEntityTransferByIdentifier(string $identifier): ?SpyWebhookInboxEntityTransfer;
}
