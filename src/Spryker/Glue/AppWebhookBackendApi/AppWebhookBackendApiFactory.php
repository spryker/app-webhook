<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi;

use Spryker\Glue\AppWebhookBackendApi\Dependency\Facade\AppWebhookBackendApiToAppWebhookFacadeInterface;
use Spryker\Glue\AppWebhookBackendApi\Mapper\Webhook\GlueRequestWebhookMapper;
use Spryker\Glue\AppWebhookBackendApi\Mapper\Webhook\GlueResponseWebhookMapper;
use Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractFactory;

/**
 * @method \Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiConfig getConfig()
 */
class AppWebhookBackendApiFactory extends AbstractFactory
{
    public function createGlueRequestWebhookMapper(): GlueRequestWebhookMapper
    {
        return new GlueRequestWebhookMapper($this->getConfig(), $this->getGlueRequestWebhookMapperPlugin());
    }

    public function createGlueResponseWebhookMapper(): GlueResponseWebhookMapper
    {
        return new GlueResponseWebhookMapper($this->getConfig());
    }

    public function getGlueRequestWebhookMapperPlugin(): ?GlueRequestWebhookMapperPluginInterface
    {
        /** @phpstan-var \Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface|null */
        return $this->getProvidedDependency(AppWebhookBackendApiDependencyProvider::PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER);
    }

    public function getAppWebhookFacade(): AppWebhookBackendApiToAppWebhookFacadeInterface
    {
        /** @phpstan-var \Spryker\Glue\AppWebhookBackendApi\Dependency\Facade\AppWebhookBackendApiToAppWebhookFacadeInterface */
        return $this->getProvidedDependency(AppWebhookBackendApiDependencyProvider::FACADE_APP_WEBHOOK);
    }
}
