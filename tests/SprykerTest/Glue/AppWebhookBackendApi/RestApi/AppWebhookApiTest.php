<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\AppWebhookBackendApi\RestApi;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Spryker\Glue\AppWebhookBackendApi\AppWebhookBackendApiDependencyProvider;
use Spryker\Glue\AppWebhookBackendApi\Controller\WebhooksController;
use Spryker\Glue\AppWebhookBackendApi\Plugin\AppWebhookBackendApi\GlueRequestWebhookMapperPluginInterface;
use Spryker\Zed\AppWebhook\AppWebhookDependencyProvider;
use SprykerTest\Glue\AppWebhookBackendApi\AppWebhookBackendApiTester;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group AppWebhookBackendApi
 * @group RestApi
 * @group AppWebhookApiTest
 * Add your own group annotations below this line
 */
class AppWebhookApiTest extends Unit
{
    use DependencyHelperTrait;

    protected AppWebhookBackendApiTester $tester;

    protected function _before(): void
    {
        parent::_before();
    }

    public function testGivenAGlueRequestWithoutContentWhenTheRequestIsHandledThenAHttpStatus400AndAnErrorMessageIsReturnedInTheGlueResponseTransfer(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();

        $webhooksController = new WebhooksController();

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_BAD_REQUEST, $glueResponseTransfer->getHttpStatus());
        $this->assertSame('POST content is required.', $glueResponseTransfer->getErrors()[0]->getMessage());
    }

    public function testGivenAValidGlueRequestWhenTheRequestIsNotHandledByAnyOfTheAttachedPluginsThenAnExceptionIsThrownHttpStatus400IsReturnedTogetherWithAMessageInTheGlueResponseTransfer(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer
            ->setContent('{"key": "value"}')
            ->setPath('/webhooks');

        $webhooksController = new WebhooksController();

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_BAD_REQUEST, $glueResponseTransfer->getHttpStatus());
        $this->assertCount(1, $glueResponseTransfer->getErrors());
    }

    public function testGivenAValidGlueRequestWhenTheRequestIsHandledAndTheWebhookResponseIsSuccessfulThenAHttpStatus200IsReturnedInTheGlueResponseTransfer(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer
            ->setContent('{"key": "value"}')
            ->setPath('/webhooks');

        $webhooksController = new WebhooksController();

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [
            $this->tester->createSuccessfulWebhookHandlerPlugin(),
        ]);

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_OK, $glueResponseTransfer->getHttpStatus());
    }

    public function testGivenAValidGlueRequestWhenTheRequestIsHandledAndTheWebhookResponseIsNotSuccessfulThenAHttpStatus400AndAMessageIsReturnedInTheGlueResponseTransfer(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer
            ->setContent('{"key": "value"}')
            ->setPath('/webhooks');

        $webhooksController = new WebhooksController();

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [
            $this->tester->createFailingWebhookHandlerPlugin('An error occurred while processing the webhook request.'),
        ]);

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_BAD_REQUEST, $glueResponseTransfer->getHttpStatus());
        $this->assertSame($glueResponseTransfer->getErrors()[0]->getMessage(), 'An error occurred while processing the webhook request.');
    }

    public function testGivenAValidGlueRequestAndAGlueRequestWebhookMapperPluginWhenTheRequestIsHandledAndTheWebhookResponseIsSuccessfulThenAHttpStatus400AndAMessageIsReturnedInTheGlueResponseTransfer(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer
            ->setContent('{"key": "value"}')
            ->setPath('/webhooks');

        $webhooksController = new WebhooksController();

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [
            $this->tester->createSuccessfulWebhookHandlerPlugin(function (WebhookRequestTransfer $webhookRequestTransfer): void {
                $this->assertSame($webhookRequestTransfer->getContent(), '{"foo": "bar"}');
            }),
        ]);

        $glueRequestWebhookMapperPlugin = new class implements GlueRequestWebhookMapperPluginInterface {
            public function mapGlueRequestDataToWebhookRequestTransfer(
                GlueRequestTransfer $glueRequestTransfer,
                WebhookRequestTransfer $webhookRequestTransfer
            ): WebhookRequestTransfer {
                $webhookRequestTransfer = new WebhookRequestTransfer();
                $webhookRequestTransfer->setContent('{"foo": "bar"}');

                return $webhookRequestTransfer;
            }
        };
        $this->tester->setDependency(AppWebhookBackendApiDependencyProvider::PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER, $glueRequestWebhookMapperPlugin);

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_OK, $glueResponseTransfer->getHttpStatus());
    }

    /**
     * Tests that the canHandle method of the webhook handler plugin is tested.
     *
     * @return void
     */
    public function testGivenOneWebhookHandlerPluginThatCanNotHandleWhenTheRequestIsHandledThenOnlyOneWebhookHandlerPluginIsExecuted(): void
    {
        // Arrange
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer
            ->setContent('{"key": "value"}')
            ->setPath('/webhooks');

        $webhooksController = new WebhooksController();

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [
            $this->tester->createSuccessfulWebhookHandlerPlugin(),
            $this->tester->createCanNotHandleWebhookHandlerPlugin(),
        ]);

        $glueRequestWebhookMapperPlugin = new class implements GlueRequestWebhookMapperPluginInterface {
            public function mapGlueRequestDataToWebhookRequestTransfer(
                GlueRequestTransfer $glueRequestTransfer,
                WebhookRequestTransfer $webhookRequestTransfer
            ): WebhookRequestTransfer {
                $webhookRequestTransfer = new WebhookRequestTransfer();
                $webhookRequestTransfer->setContent('{"foo": "bar"}');

                return $webhookRequestTransfer;
            }
        };
        $this->tester->setDependency(AppWebhookBackendApiDependencyProvider::PLUGIN_GLUE_REQUEST_WEBHOOK_MAPPER, $glueRequestWebhookMapperPlugin);

        // Act
        $glueResponseTransfer = $webhooksController->postAction($glueRequestTransfer);

        // Assert
        $this->assertSame(Response::HTTP_OK, $glueResponseTransfer->getHttpStatus());
    }
}
