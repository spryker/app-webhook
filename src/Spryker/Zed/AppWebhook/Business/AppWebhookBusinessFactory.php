<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business;

use Spryker\Zed\AppWebhook\AppWebhookDependencyProvider;
use Spryker\Zed\AppWebhook\Business\Identifier\IdentifierBuilder;
use Spryker\Zed\AppWebhook\Business\Identifier\IdentifierBuilderInterface;
use Spryker\Zed\AppWebhook\Business\WebhookHandler\WebhookHandler;
use Spryker\Zed\AppWebhook\Business\WebhookProcessor\WebhookDeleter;
use Spryker\Zed\AppWebhook\Business\WebhookProcessor\WebhookProcessor;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookRepositoryInterface getRepository()
 * @method \Spryker\Zed\AppWebhook\AppWebhookConfig getConfig()
 */
class AppWebhookBusinessFactory extends AbstractBusinessFactory
{
    public function createWebhookHandler(): WebhookHandler
    {
        return new WebhookHandler(
            $this->getAppWebhookHandlerPlugins(),
            $this->getConfig(),
            $this->getEntityManager(),
            $this->createIdentifierBuilder(),
        );
    }

    public function createWebhookProcessor(): WebhookProcessor
    {
        return new WebhookProcessor(
            $this->createWebhookHandler(),
            $this->getRepository(),
        );
    }

    public function createWebhookDeleter(): WebhookDeleter
    {
        return new WebhookDeleter(
            $this->getEntityManager(),
        );
    }

    public function createIdentifierBuilder(): IdentifierBuilderInterface
    {
        return new IdentifierBuilder();
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
