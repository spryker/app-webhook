<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\AppWebhook\AppWebhookConfig getConfig()
 */
class AppWebhookDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGINS_WEBHOOK_HANDLER = 'APP_WEBHOOK:PLUGINS_WEBHOOK_HANDLER';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        return $this->addWebhookHandlerPlugins($container);
    }

    protected function addWebhookHandlerPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_WEBHOOK_HANDLER, function (): array {
            // @codeCoverageIgnoreStart
            return $this->getWebhookHandlerPlugins();
            // @codeCoverageIgnoreEnd
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface>
     */
    protected function getWebhookHandlerPlugins(): array
    {
        // Only set in tests and on project level.
        // @codeCoverageIgnoreStart
        return [];
        // @codeCoverageIgnoreEnd
    }
}
