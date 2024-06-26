<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;

/**
 * Use this plugin to extract data from the GlueRequestTransfer and map it to the WebhookRequestTransfer.
 * This could be:
 * - Tenant Identifier
 * - Merchant Reference
 * - Transaction Identifier
 * - etc.
 */
interface GlueRequestWebhookMapperPluginInterface
{
    public function mapGlueRequestDataToWebhookRequestTransfer(
        GlueRequestTransfer $glueRequestTransfer,
        WebhookRequestTransfer $webhookRequestTransfer
    ): WebhookRequestTransfer;
}
