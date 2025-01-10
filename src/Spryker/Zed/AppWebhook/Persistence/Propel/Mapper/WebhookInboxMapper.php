<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\SpyWebhookInboxEntityTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Orm\Zed\AppWebhook\Persistence\SpyWebhookInbox;

class WebhookInboxMapper
{
    public function mapWebhookInboxEntityToWebhookRequestTransfer(
        SpyWebhookInbox $spyWebhookInbox,
        WebhookRequestTransfer $webhookRequestTransfer
    ): WebhookRequestTransfer {
        $webhookRequestTransfer->fromArray(json_decode($spyWebhookInbox->getWebhook(), true));
        $webhookRequestTransfer->setRetries($spyWebhookInbox->getRetries());

        return $webhookRequestTransfer;
    }

    public function mapWebhookInboxEntityToWebhookInboxEntityTransfer(
        SpyWebhookInbox $spyWebhookInbox,
        SpyWebhookInboxEntityTransfer $spyWebhookInboxEntityTransfer
    ): SpyWebhookInboxEntityTransfer {
        $spyWebhookInboxEntityTransfer->fromArray($spyWebhookInbox->toArray(), true);

        return $spyWebhookInboxEntityTransfer;
    }
}
