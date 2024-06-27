<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi;

use Spryker\Glue\AppWebhookBackendApi\Dependency\Facade\AppWebhookBackendApiToAppWebhookFacadeBridge;
use Spryker\Glue\AppWebhookBackendApi\Dependency\Facade\AppWebhookBackendApiToAppWebhookFacadeInterface;
use Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Backend\Container;

/**
 * @method \Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiConfig getConfig()
 */
class AppWebhookBackendApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_APP_WEBHOOK = 'APP_WEBHOOK_BACKEND_API:FACADE_APP_WEBHOOK';

    /**
     * @var string
     */
    public const PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER = 'APP_WEBHOOK_BACKEND_API:PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER';

    public function provideBackendDependencies(Container $container): Container
    {
        $container = parent::provideBackendDependencies($container);
        $container = $this->addGlueRequestWebhookMapperPlugin($container);
        $container = $this->addAppWebhookFacade($container);

        return $container;
    }

    protected function addGlueRequestWebhookMapperPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER, function (): ?GlueRequestWebhookMapperPluginInterface {
            return $this->getGlueRequestWebhookMapperPlugin();
        });

        return $container;
    }

    protected function getGlueRequestWebhookMapperPlugin(): ?GlueRequestWebhookMapperPluginInterface
    {
        return null;
    }

    protected function addAppWebhookFacade(Container $container): Container
    {
        $container->set(static::FACADE_APP_WEBHOOK, static function (Container $container): AppWebhookBackendApiToAppWebhookFacadeInterface {
            // The AppWebhookFacade will always be mocked
            // @codeCoverageIgnoreStart
            return new AppWebhookBackendApiToAppWebhookFacadeBridge($container->getLocator()->appWebhook()->facade());
            // @codeCoverageIgnoreEnd
        });

        return $container;
    }
}
