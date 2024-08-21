<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\AppWebhook\Helper;

use Closure;
use Codeception\Module;
use Exception;
use Generated\Shared\Transfer\WebhookRequestTransfer;
use Generated\Shared\Transfer\WebhookResponseTransfer;
use Orm\Zed\AppWebhook\Persistence\SpyWebhookInbox;
use Orm\Zed\AppWebhook\Persistence\SpyWebhookInboxQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\AppWebhook\Dependency\Plugin\WebhookHandlerPluginInterface;

class AppWebhookHelper extends Module
{
    public function createSuccessfulWebhookHandlerPlugin(?Closure $callable = null): WebhookHandlerPluginInterface
    {
        return new class ($callable) implements WebhookHandlerPluginInterface {
            public function __construct(protected ?Closure $callable = null)
            {
            }

            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return true;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                if ($this->callable instanceof Closure) {
                    $webhookResponseTransfer = ($this->callable)($webhookRequestTransfer, $webhookResponseTransfer);
                }

                return $webhookResponseTransfer->setIsSuccessful(true);
            }
        };
    }

    public function createFailingWebhookHandlerPlugin(string $message): WebhookHandlerPluginInterface
    {
        return new class ($message) implements WebhookHandlerPluginInterface {
            public function __construct(protected string $message)
            {
            }

            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return true;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                return $webhookResponseTransfer
                    ->setIsSuccessful(false)
                    ->setMessage($this->message);
            }
        };
    }

    public function createCanNotHandleWebhookHandlerPlugin(): WebhookHandlerPluginInterface
    {
        return new class implements WebhookHandlerPluginInterface {
            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return false;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                return $webhookResponseTransfer;
            }
        };
    }

    public function createExceptionThrowingWebhookHandlerPlugin(string $message = 'Something went wrong'): WebhookHandlerPluginInterface
    {
        return new class ($message) implements WebhookHandlerPluginInterface {
            public function __construct(protected string $message)
            {
            }

            public function canHandle(WebhookRequestTransfer $webhookRequestTransfer): bool
            {
                return true;
            }

            public function handleWebhook(
                WebhookRequestTransfer $webhookRequestTransfer,
                WebhookResponseTransfer $webhookResponseTransfer
            ): WebhookResponseTransfer {
                throw new Exception($this->message);
            }
        };
    }

    public function haveWebhookRequestPersisted(WebhookRequestTransfer $webhookRequestTransfer, string $message = ''): void
    {
        $spyWebhookInboxEntity = new SpyWebhookInbox();
        $spyWebhookInboxEntity
            ->setIdentifier($webhookRequestTransfer->getIdentifierOrFail())
            ->setWebhook(json_encode($webhookRequestTransfer->toArray()))
            ->setMessage($message)
            ->setSequenceNumber($this->getSequenceNumber($webhookRequestTransfer->getIdentifierOrFail()));

        $spyWebhookInboxEntity->save();
    }

    protected function getSequenceNumber(string $identifier): int
    {
        $spyWebhookInboxEntity = SpyWebhookInboxQuery::create()
            ->filterByIdentifier($identifier)
            ->orderBySequenceNumber(Criteria::DESC)
            ->findOne();

        return $spyWebhookInboxEntity ? $spyWebhookInboxEntity->getSequenceNumber() + 1 : 0;
    }

    public function assertWebhookIsPersisted(string $identifier, int $sequenceNumber = 0): void
    {
        $spyWebhookInboxEntity = SpyWebhookInboxQuery::create()
            ->filterByIdentifier($identifier)
            ->filterBySequenceNumber($sequenceNumber)
            ->findOne();

        $this->assertNotNull($spyWebhookInboxEntity, 'Webhook entity not found in the database.');
    }

    public function assertWebhookIsNotPersisted(string $identifier, ?int $sequenceNUmber = null): void
    {
        $spyWebhookInboxQuery = SpyWebhookInboxQuery::create()
            ->filterByIdentifier($identifier);

        if ($sequenceNUmber) {
            $spyWebhookInboxQuery->filterBySequenceNumber($sequenceNUmber);
        }

        $spyWebhookInboxEntity = $spyWebhookInboxQuery->findOne();

        $this->assertNull($spyWebhookInboxEntity, 'Webhook entity was not expected to be found in the database but is found.');
    }

    public function assertPersistedWebhookHasMessage(string $identifier, string $expectedMessage, int $sequenceNumber = 0): void
    {
        $spyWebhookInboxEntity = SpyWebhookInboxQuery::create()
            ->filterByIdentifier($identifier)
            ->filterBySequenceNumber($sequenceNumber)
            ->findOne();

        $this->assertNotNull($spyWebhookInboxEntity, 'Webhook entity not found in the database.');
        $this->assertSame($expectedMessage, $spyWebhookInboxEntity->getMessage());
    }

    public function assertPersistedWebhookRetries(string $identifier, int $expectedRetries, int $sequenceNumber = 0): void
    {
        $spyWebhookInboxEntity = SpyWebhookInboxQuery::create()
            ->filterByIdentifier($identifier)
            ->filterBySequenceNumber($sequenceNumber)
            ->findOne();

        $this->assertNotNull($spyWebhookInboxEntity, 'Webhook entity not found in the database.');
        $this->assertSame($expectedRetries, $spyWebhookInboxEntity->getRetries());
    }
}
