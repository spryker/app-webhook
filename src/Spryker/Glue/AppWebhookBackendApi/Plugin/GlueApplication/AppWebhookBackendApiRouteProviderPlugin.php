<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\AppWebhookBackendApi\Plugin\GlueApplication;

use Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiConfig;
use Spryker\Glue\AppWebhookBackendApi\Controller\WebhooksController;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouteProviderPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @codeCoverageIgnore This class will only be used when caching is disabled. Without this Plugin the Webhook requests would fail, and we would see an issue right away.
 */
class AppWebhookBackendApiRouteProviderPlugin extends AbstractPlugin implements RouteProviderPluginInterface
{
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection->add('postWebhookLiveMode', $this->getPostWebhookRouteLiveMode());
        $routeCollection->add('postWebhookTestMode', $this->getPostWebhookRouteTestMode());

        return $routeCollection;
    }

    public function getPostWebhookRouteLiveMode(): Route
    {
        return (new Route(AppWebhookBackendApiConfig::WEBHOOK_ENDPOINT_PATH))
            ->setDefaults([
                '_controller' => [WebhooksController::class, 'postAction'],
                '_resourceName' => 'Payment',
            ])
            ->setMethods(Request::METHOD_POST);
    }

    public function getPostWebhookRouteTestMode(): Route
    {
        return (new Route(AppWebhookBackendApiConfig::WEBHOOK_ENDPOINT_PATH_TEST))
            ->setDefaults([
                '_controller' => [WebhooksController::class, 'postAction'],
                '_resourceName' => 'Payment',
            ])
            ->setMethods(Request::METHOD_POST);
    }
}
