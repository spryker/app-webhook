<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Mapper\Webhook;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiConfig;
use Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface;

class GlueRequestWebhookMapper
{
    public function __construct(
        protected AppWebhookBackendApiConfig $appWebhookBackendApiConfig,
        protected ?GlueRequestWebhookMapperPluginInterface $glueRequestWebhookMapperPlugin = null
    ) {
    }

    public function mapGlueRequestTransferToWebhookRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer
    ): WebhookRequestTransfer {
        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer->setContent($glueRequestTransfer->getContent());
        $webhookRequestTransfer->setMode(
            $this->appWebhookBackendApiConfig->getModeByEndpointPath($glueRequestTransfer->getPathOrFail()),
        );

        // This can be set on project level to manipulate the webhook request transfer to e.g. add additional data.
        if ($this->glueRequestWebhookMapperPlugin instanceof GlueRequestWebhookMapperPluginInterface) {
            return $this->glueRequestWebhookMapperPlugin->mapGlueRequestDataToWebhookRequestTransfer($glueRequestTransfer, $webhookRequestTransfer);
        }

        return $webhookRequestTransfer;
    }
}
