# AppWebhook Package
[![Latest Stable Version](https://poser.pugx.org/spryker/app-webhook/v/stable.svg)](https://packagist.org/packages/spryker/app-webhook)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)

Provides SyncAPI and AsyncAPI schema files and the needed code to make the Mini-Framework an App.

## Installation

```
composer require spryker/app-webhook
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

# Documentation

# High-Level Architecture

[<img alt="AppWebhook High-Level Architecture" width="auto" src="docs/images/app-webhook-high-level-architecture.svg" />](https://docs.spryker.com/)


## Features

### Handling of external Webhook requests
This package provides all functionality that is needed to receive webhook requests from external applications. This package basically does nothing than providing a stack to add webhook handler from any package that has to deal with webhooks.

When a webhook is received but no handler is executed a failed response will be returned to the caller.

When a webhook was handled a successful response will be returned to the caller.

# Extension

## Glue
Glue offers the following extension points

- `\Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface`

### GlueRequestWebhookMapperPluginInterface
This plugin interface can be used in your application to map some information from the request to the `WebhookRequestTransfer` object which you can use to forward request data like headers into your application. You can also extract information out of the request body and set attributes on the `WebhookRequestTransfer`. For this, you need to add your attributes to the `WebhookRequestTransfer` as you already know from SCOS implementations Create, use, and extend the transfer objects | Spryker Documentation

## Zed
Zed offers the following extension points:

- `\Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface`

### WebhookHandlerPluginInterface
This plugin needs to be implemented in your application which should handle specific webhook requests. The plugin offers two methods for you:

- `canHandle(WebhookRequestTransfer $webhookRequestTransfer)`
- `handle(WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer)`

The `canHandle()` method returns a boolean indicating if this plugin can handle the current request. F.e. you have two different handlers in your application, one can handle foo events and the other one can handle bar events. You can combine the

`GlueRequestWebhookMapperPluginInterface` that extracts the event name from the request and sets it in the `WebhookRequestTransfer` e.g. as eventName your `WebhookHandlerPluginInterface::canHandle()` can make use of this value and returns the boolean to indicate this plugin can handle the current request or not.

