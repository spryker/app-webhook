<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Generated\Shared\Transfer\SpyWebhookInboxEntityTransfer;
use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;

interface AppWebhookEntityManagerInterface
{
    public function saveWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer): void;

//    public function updateWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): void;

    public function deleteWebhookRequest(WebhookRequestTransfer $webhookRequestTransfer): void;

    public function deleteWebhookRequests(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void;

    public function updateWebhookInboxEntity(SpyWebhookInboxEntityTransfer $spyWebhookInboxEntityTransfer): void;
}
