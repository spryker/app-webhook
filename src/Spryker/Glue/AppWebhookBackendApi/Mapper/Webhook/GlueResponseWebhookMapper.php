<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Mapper\Webhook;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiConfig;
use Symfony\Component\HttpFoundation\Response;

class GlueResponseWebhookMapper
{
    public function __construct(
        protected AppWebhookBackendApiConfig $appWebhookBackendApiConfig
    ) {
    }

    public function mapWebhookResponseTransferToSingleResourceGlueResponseTransfer(
        WebhookResponseTransfer $webhookResponseTransfer
    ): GlueResponseTransfer {
        $glueResponseTransfer = new GlueResponseTransfer();

        $glueResourceTransfer = new GlueResourceTransfer();
        $glueResourceTransfer->setAttributes($glueResponseTransfer);
        $glueResourceTransfer->setType($this->appWebhookBackendApiConfig->getResourceType());

        $glueResponseTransfer->addResource($glueResourceTransfer);
        $glueResponseTransfer->setHttpStatus(Response::HTTP_OK);

        if ($webhookResponseTransfer->getIsSuccessful() !== true) {
            $glueResponseTransfer->setHttpStatus(Response::HTTP_BAD_REQUEST);
            $glueResponseTransfer->addError((new GlueErrorTransfer())->setMessage($webhookResponseTransfer->getMessage()));
        }
        $glueResponseTransfer->setContent($webhookResponseTransfer->getContent());

        return $glueResponseTransfer;
    }
}
