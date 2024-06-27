<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business;

use Spryker\Zed\AppWebhook\AppWebhookDependencyProvider;
use Spryker\Zed\AppWebhook\Business\WebhookHandler\WebhookHandler;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class AppWebhookBusinessFactory extends AbstractBusinessFactory
{
    public function createWebhookHandler(): WebhookHandler
    {
        return new WebhookHandler(
            $this->getAppWebhookHandlerPlugins(),
        );
    }

    /**
     * @return array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface>
     */
    public function getAppWebhookHandlerPlugins(): array
    {
        /** @phpstan-var array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface> */
        return $this->getProvidedDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER);
    }
}
