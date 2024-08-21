<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\AppWebhook\Business;

use Codeception\Stub;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\WebhookInboxCriteriaTransfer;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Ramsey\Uuid\Uuid;
use Spryker\Zed\AppWebhook\AppWebhookDependencyProvider;
use Spryker\Zed\AppWebhook\Business\Identifier\IdentifierBuilderInterface;
use SprykerTest\Zed\AppWebhook\AppWebhookBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group AppWebhook
 * @group Business
 * @group Facade
 * @group AppWebhookFacadeTest
 * Add your own group annotations below this line
 */
class AppWebhookFacadeTest extends Unit
{
    protected AppWebhookBusinessTester $tester;

    /**
     * Covers any exceptional case. It ensures that we always have the webhook data in the database either for later processing or failure investigation.
     */
    public function testGivenAValidWebhookThatAndAnExceptionInTheWebhookHandlerPluginWhenTheWebhookIsProcessedThenItIsPersistedForLaterProcessingWithTheDefaultIdentifierAndIsUpdatedWithTheExceptionMessage(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString();
        $expectedExceptionMessage = 'Something went wrong';

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $webhookHandlerPlugin = $this->tester->createExceptionThrowingWebhookHandlerPlugin($expectedExceptionMessage);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        // This also asserts that by default the sequence_number is set to 0
        $this->tester->assertPersistedWebhookHasMessage($defaultIdentifier, $expectedExceptionMessage);
    }

    public function testGivenAValidWebhookThatCanNotBeProcessedNowAndCanBeProcessedLaterWhenTheWebhookIsProcessedThenItIsPersistedForLaterProcessing(): void
    {
        // Arrange
        $identifier = Uuid::uuid4()->toString();

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($identifier) {
            $webhookResponseTransfer
                ->setIsHandled(false)
                ->setIdentifier($identifier);

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertFalse($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is not handled and persisted for later processing but it is not.');
        $this->assertSame($webhookResponseTransfer->getIdentifier(), $identifier);

        // This also asserts that by default sequence_number is set to 0
        $this->tester->assertWebhookIsPersisted($identifier);
    }

    public function testGivenAValidWebhookThatCanBeProcessedNowWhenTheWebhookIsProcessedAndTheWebhookResponseReturnsIsHandledTrueThenItIsRemovedFromPersistence(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString();

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($defaultIdentifier) {
            $webhookResponseTransfer
                ->setIsHandled(true);

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertTrue($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is handled but it was not marked as handled.');

        // This also asserts that by default sequence_number is set to 0
        $this->tester->assertWebhookIsNotPersisted($defaultIdentifier);
    }

    public function testGivenAValidWebhookThatCanBeProcessedNowWhenTheWebhookIsProcessedAndTheWebhookResponseReturnsIsHandledNullThenItIsRemovedFromPersistence(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString();

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($defaultIdentifier) {
            // By default, isHandled is null and thus not needed to be set here.
            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertNull($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is handled is set to null (default).');

        // This also asserts that by default sequence_number is set to 0
        $this->tester->assertWebhookIsNotPersisted($defaultIdentifier);
    }

    public function testGivenAValidWebhookThatCanBeProcessedNowWhenTheWebhookIsProcessedAndTheWebhookResponseReturnsIsHandledNullAndIsSuccessfulFalseThenItIsNotRemovedFromPersistence(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString();

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $webhookHandlerPlugin = $this->tester->createFailingWebhookHandlerPlugin('Something went wrong');

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertFalse($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is not successful but is.');
        $this->assertNull($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is handled is set to null (default).');

        // This also asserts that by default sequence_number is set to 0
        $this->tester->assertWebhookIsPersisted($defaultIdentifier);
    }

    public function testGivenAnUnprocessedWebhookRequestExistsInTheDatabaseAndAnotherValidWebhookThatCanNotBeProcessedNowAndCanBeProcessedLaterWhenTheWebhookIsProcessedThenItIsPersistedForLaterProcessingWithTheNextAvailableSequenceNumber(): void
    {
        // Arrange
        $identifier = Uuid::uuid4()->toString();

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async')
            ->setIdentifier($identifier);

        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer, 'First unprocessed webhook');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($identifier) {
            $webhookResponseTransfer
                ->setIsHandled(false)
                ->setIdentifier($identifier)
                ->setMessage('Second unprocessed webhook');

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertFalse($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is not handled and persisted for later processing but it is not.');
        $this->assertSame($webhookResponseTransfer->getIdentifier(), $identifier);

        // This also asserts correct sequence_number creation
        $this->tester->assertPersistedWebhookHasMessage($identifier, 'First unprocessed webhook', 0);
        $this->tester->assertPersistedWebhookHasMessage($identifier, 'Second unprocessed webhook', 1);
    }

    public function testGivenAnUnprocessedWebhookRequestWhenItWillBeRetriedThenNoNewEntityIsPersisted(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString(); // The one used when we start the handling process
        $identifier = Uuid::uuid4()->toString(); // The one that is already persisted

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async')
            ->setIdentifier($identifier)
            ->setIsRetry(true);

        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer, 'First message');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($identifier) {
            $webhookResponseTransfer
                ->setIsHandled(false)
                ->setIdentifier($identifier)
                ->setMessage('Second message');

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertFalse($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is not handled and persisted for later processing but it is not.');
        $this->assertSame($webhookResponseTransfer->getIdentifier(), $identifier);

        // Ensure that no new unprocessed entity is persisted
        $this->tester->assertWebhookIsNotPersisted($defaultIdentifier);
    }

    public function testGivenAnUnprocessedWebhookRequestWhenItWillBeRetriedAndStillCanNotBeProcessedThenTheNumberOfRetriesIsUpdated(): void
    {
        // Arrange
        $defaultIdentifier = Uuid::uuid4()->toString(); // The one used when we start the handling process
        $identifier = Uuid::uuid4()->toString(); // The one that is already persisted

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async')
            ->setIdentifier($identifier)
            ->setIsRetry(true);

        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer, 'First message');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($identifier) {
            $webhookResponseTransfer
                ->setIsHandled(false)
                ->setIdentifier($identifier)
                ->setMessage('Second message');

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);
        $this->tester->mockFactoryMethod('createIdentifierBuilder', Stub::makeEmpty(IdentifierBuilderInterface::class, [
            'getIdentifier' => $defaultIdentifier,
        ]));

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertFalse($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is not handled and persisted for later processing but it is not.');
        $this->assertSame($webhookResponseTransfer->getIdentifier(), $identifier);

        // Ensure that no new unprocessed entity is persisted
        $this->tester->assertWebhookIsNotPersisted($defaultIdentifier);
    }

    public function testGivenAnUnprocessedWebhookRequestWhenItWillBeRetriedAndIsSuccessfullyProcessedThenItWillBeDeleted(): void
    {
        // Arrange
        $identifier = Uuid::uuid4()->toString(); // The one that is already persisted

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent('{foo: bar}')
            ->setMode('async')
            ->setIdentifier($identifier)
            ->setIsRetry(true);

        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer, 'First message');

        $webhookResponseTransfer = new WebhookResponseTransfer();

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use ($identifier) {
            $webhookResponseTransfer
                ->setIsHandled(true);

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);

        // Act
        $webhookResponseTransfer = $this->tester->getFacade()->handleWebhook($webhookRequestTransfer, $webhookResponseTransfer);

        // Assert
        $this->assertTrue($webhookResponseTransfer->getIsSuccessful(), 'Expected that the webhook is marked as successful but is not.');
        $this->assertTrue($webhookResponseTransfer->getIsHandled(), 'Expected that the webhook is handled but it is not.');

        // Ensure that the now handled WebhookRequest is deleted from the persistence.
        $this->tester->assertWebhookIsNotPersisted($identifier);
    }

    /**
     * @group single
     */
    public function testGivenUnprocessedWebhookRequestsForAnIdentifierWhenThoseAreProcessedThenTheyAreProcessedInTheOrderOfTheirSequenceNumber(): void
    {
        // Arrange
        $identifier = Uuid::uuid4()->toString();

        $identifierForSequenceNumberProcessing = [
            'First',
            'Second',
            'Third',
        ];

        $webhookRequestTransfer = new WebhookRequestTransfer();
        $webhookRequestTransfer
            ->setContent($identifierForSequenceNumberProcessing[0])
            ->setMode('async')
            ->setIdentifier($identifier);

        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer);

        $webhookRequestTransfer->setContent($identifierForSequenceNumberProcessing[1]);
        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer);

        $webhookRequestTransfer->setContent($identifierForSequenceNumberProcessing[2]);
        $this->tester->haveWebhookRequestPersisted($webhookRequestTransfer);

        $sequenceNumber = 0;

        $callable = function (WebhookRequestTransfer $webhookRequestTransfer, WebhookResponseTransfer $webhookResponseTransfer) use (&$sequenceNumber, $identifierForSequenceNumberProcessing) {
            $this->assertSame($identifierForSequenceNumberProcessing[$sequenceNumber], $webhookRequestTransfer->getContent());
            $sequenceNumber++;

            return $webhookResponseTransfer;
        };

        $webhookHandlerPlugin = $this->tester->createSuccessfulWebhookHandlerPlugin($callable);

        $this->tester->setDependency(AppWebhookDependencyProvider::PLUGINS_WEBHOOK_HANDLER, [$webhookHandlerPlugin]);

        $webhookInboxCriteriaTransfer = new WebhookInboxCriteriaTransfer();
        $webhookInboxCriteriaTransfer->addIdentifier($identifier);

        // Act | Assert
        $this->tester->getFacade()->processUnprocessedWebhooks($webhookInboxCriteriaTransfer);

        $this->tester->assertWebhookIsNotPersisted($identifier, 0);
        $this->tester->assertWebhookIsNotPersisted($identifier, 1);
        $this->tester->assertWebhookIsNotPersisted($identifier, 2);
    }
}
