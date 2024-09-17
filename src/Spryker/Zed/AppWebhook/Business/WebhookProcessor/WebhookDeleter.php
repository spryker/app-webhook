<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\WebhookProcessor;

use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Spryker\Zed\AppWebhook\Persistence\AppWebhookEntityManagerInterface;

class WebhookDeleter
{
    public function __construct(protected AppWebhookEntityManagerInterface $appWebhookEntityManager)
    {
    }

    public function deleteWebhooks(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void
    {
        $this->appWebhookEntityManager->deleteWebhookRequests($webhookInboxCriteriaTransfer);
    }
}
