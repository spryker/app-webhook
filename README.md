# AppWebhook Module
[![Latest Stable Version](https://poser.pugx.org/spryker/app-webhook/v/stable.svg)](https://packagist.org/packages/spryker/app-webhook)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)

Provides SyncAPI and AsyncAPI schema files and the needed code to make the Mini-Framework an App with Webhook capabilities.

## Installation

```
composer require spryker/app-webhook
```

### Configure

#### App Identifier

config/Shared/config_default.php

```
use Spryker\Shared\AppWebhook\AppConstants;

$config[AppConstants::APP_IDENTIFIER] = getenv('APP_IDENTIFIER') ?: 'hello-world';
```

### Testing the AppWebhook

You can test the AppWebhook as usual with Codeception. Before that you need to run some commands:

```
composer setup
```

With these commands you've set up the AppWebhook and can start the tests

```
vendor/bin/codecept build
vendor/bin/codecept run
```

### Documentation

### Webhook handling

This module is responsible to receive and handle webhooks. The module provides a controller that can be used to handle incoming webhooks.

The API endpoint is `/webhooks` and the controller is `WebhooksController` inside the Glue Application. This package is not handling webhooks on its own you must implment the logic to handle the webhooks via the provided `\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface` see description down below.

#### Process in a Nutshell

- A webhook is received by the `WebhooksController` and the `WebhookRequestTransfer` is created.
- The webhook content is mapped to a `WebhookRequestTransfer`.
- The `WebhookRequestTransfer` is passed together with a `WebhookResponseTransfer` to the `\Spryker\Glue\AppWebhookBackendApi\Dependency\Facade\AppWebhookBackendApiToAppWebhookFacadeInterface::handleWebhook()` method.
- The `handleWebhook` method does:
  - Creates an identifier for this specific webhook.
  - When it is not a retried webhook the webhook will be persisted in the database.
  - In case of a retried webhook the webhook will be fetched from the database.
  - When the number of retries exceeds the configurable allowed number of retries the webhook will be removed from the database and an exception will be thrown.
  - Find the correct handler for the webhook and call the `handle` method of the handler.
  - When the handler returns a failed `WebhookResponseTransfer` the response will be persisted in the database.
  - When the handler returns a not handled `WebhookResponseTransfer` the response will be persisted in the database with a message that was provided by the implementation of the plugin.
  - When the handler throws an exception the exception message will be persisted in the database.
  - When the handler returns a successful `WebhookResponseTransfer` the webhook will be removed from the database.
  - It returns the `WebhookResponseTransfer` to the controller.
- The controller formats the `WebhookResponseTransfer` into a Glue response which will be either:
  - 200 OK in case of everything went well.
  - 400 BAD REQUEST in case of a failed response.

#### Retry Mechanism

In some case of a webhook can not be handled it will be persisted in the database and will be retried with the next incoming webhook. The number of retries is configurable and can be set in the `AppWebhookConfig::getAllowedNumberOfRetries()` method.

There are numerous reasons when a webhook may fail. An exception is thrown, the called plugin implemntation returns a failed response or the plugin implementation returns a not handled response.

Another case could be an event is sent to the application before it is ready to handle it. F.e. in the PreOrder payment of a PSP the order is not persisted yet and has no order-reference but the PSP sends a webhook to do something with the order-reference the system has to wait until it can process the webhook.

##### Future improvements for the Retry mechanism

It may be helpful in the future to provide a console command that can be used to retry failed webhooks. This command can be used to retry all failed webhooks or only a specific webhook.

### Configuration

Currently only the number of allowed retries can be configured. The configuration can be found in the `AppWebhookConfig` class.

### Plugins

### GlueApplication

#### \Spryker\Glue\AppWebhookBackendApi\Plugin\GlueApplication\AppWebhookBackendApiRouteProviderPlugin

This plugin provides the routes for the AppWebhookBackendApi module.

### Extensions

#### \Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface

This plugin can be iomplemented by any other module and has two methods:

- `\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface::canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool`
- `\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface::handle(WebhookRequestTransfer $webhookRequestTransfer): WebhookResponseTransfer`

The `canHandle` method is used to check if a webhook can be handled by a specific module. F.e. you have two handlers one for `order.created` and one for `order.updated` you can check in the `canHandle` method if the webhook can be handled by the module and return true or false.

The `handle` method is used to handle the webhook. The method is called if the `canHandle` method returns true. The method should return a `WebhookResponseTransfer` with the status of the webhook handling.
