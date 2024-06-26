<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Controller;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Glue\Kernel\Backend\Controller\AbstractController;

/**
 * @method \Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiFactory getFactory()
 */
class WebhooksController extends AbstractController
{
    public function postAction(GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        if (!$glueRequestTransfer->getContent()) {
            return (new GlueResponseTransfer())
                ->setHttpStatus(400)
                ->addError((new GlueErrorTransfer())->setMessage('POST content is required.'));
        }

        $webhookRequestTransfer = $this->getFactory()->createGlueRequestWebhookMapper()->mapGlueRequestTransferToWebhookRequestTransfer($glueRequestTransfer);
        $webhookResponseTransfer = $this->getFactory()->getAppWebhookFacade()->handleWebhook($webhookRequestTransfer, new WebhookResponseTransfer());

        return $this->getFactory()->createGlueResponseWebhookMapper()->mapWebhookResponseTransferToSingleResourceGlueResponseTransfer($webhookResponseTransfer);
    }
}
