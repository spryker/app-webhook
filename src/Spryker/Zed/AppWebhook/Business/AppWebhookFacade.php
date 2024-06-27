<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\AppWebhook\Business\AppWebhookBusinessFactory getFactory()
 */
class AppWebhookFacade extends AbstractFacade implements AppWebhookFacadeInterface
{
    /**
     * @api
     *
     * @inheritDoc
     */
    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        return $this->getFactory()->createWebhookHandler()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
    }
}
