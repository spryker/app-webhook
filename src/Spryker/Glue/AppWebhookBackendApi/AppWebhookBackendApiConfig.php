<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi;

use Spryker\Glue\Kernel\AbstractBundleConfig;

class AppWebhookBackendApiConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const MODE_TEST = 'test';

    /**
     * @var string
     */
    public const MODE_LIVE = 'live';

    /**
     * @var string
     */
    public const WEBHOOK_ENDPOINT_PATH = '/webhooks';

    /**
     * This method is used in the GlueResponseWebhookMapper to set the resource type.
     *
     * @api
     */
    public function getResourceType(): string
    {
        return 'overwrite on project level';
    }

    /**
     * @api
     *
     * @var string
     */
    public const WEBHOOK_ENDPOINT_PATH_TEST = '/webhooks/test';

    public function getModeByEndpointPath(string $endpointPath): string
    {
        return match ($endpointPath) {
            static::WEBHOOK_ENDPOINT_PATH_TEST => static::MODE_TEST,
            static::WEBHOOK_ENDPOINT_PATH => static::MODE_LIVE,
            default => '',
        };
    }
}
