<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\WebhookProcessor;

use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\AppWebhook\Business\WebhookHandler\WebhookHandler;
use Spryker\Zed\AppWebhook\Persistence\AppWebhookRepositoryInterface;

class WebhookProcessor
{
    public function __construct(protected WebhookHandler $webhookHandler, protected AppWebhookRepositoryInterface $appWebhookRepository)
    {
    }

    public function processUnprocessedWebhooks(WebhookInboxCriteriaTransfer $webhookInboxCriteriaTransfer): void
    {
        $webhookRequestTransfers = $this->appWebhookRepository->getUnprocessedWebhookRequests($webhookInboxCriteriaTransfer);

        foreach ($webhookRequestTransfers as $webhookRequestTransfer) {
            $webhookRequestTransfer->setIsRetry(true);
            $this->webhookHandler->handleWebhook($webhookRequestTransfer, new WebhookResponseTransfer());
        }
    }
}
