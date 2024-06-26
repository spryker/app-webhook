<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;

class AppWebhookBackendApiToAppWebhookFacadeBridge implements AppWebhookBackendApiToAppWebhookFacadeInterface
{
    /**
     * @var \Spryker\Zed\AppWebhook\Business\AppWebhookFacadeInterface
     */
    protected $appWebhookFacade;

    /**
     * @param \Spryker\Zed\AppWebhook\Business\AppWebhookFacadeInterface $appWebhookFacade
     */
    public function __construct($appWebhookFacade)
    {
        $this->appWebhookFacade = $appWebhookFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\WebhookRequestTransfer $webhookRequestTransfer
     * @param \Generated\Shared\Transfer\WebhookResponseTransfer $webhookResponseTransfer
     *
     * @return \Generated\Shared\Transfer\WebhookResponseTransfer
     */
    public function handleWebhook(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer): WebhookResponseTransfer
    {
        return $this->appWebhookFacade->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);
    }
}
