<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Persistence;

use Orm\Zed\AppWebhook\Persistence\SpyWebhookInbox;
use Orm\Zed\AppWebhook\Persistence\SpyWebhookInboxQuery;
use Spryker\Zed\AppWebhook\Persistence\Propel\Mapper\WebhookInboxMapper;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\AppWebhook\Persistence\AppWebhookRepositoryInterface getRepository()
 */
class AppWebhookPersistenceFactory extends AbstractPersistenceFactory
{
    public function createWebhookInboxQuery(): SpyWebhookInboxQuery
    {
        return SpyWebhookInboxQuery::create();
    }

    public function createWebhookInboxEntity(): SpyWebhookInbox
    {
        return new SpyWebhookInbox();
    }

    public function createWebhookInboxMapper(): WebhookInboxMapper
    {
        return new WebhookInboxMapper();
    }
}
