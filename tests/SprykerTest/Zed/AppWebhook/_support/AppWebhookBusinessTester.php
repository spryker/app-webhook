<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\AppWebhook;

use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 *
 * @method \Spryker\Zed\AppWebhook\Business\AppWebhookFacadeInterface getFacade(?string $moduleName = NULL)
 * @method \Spryker\Zed\AppWebhook\AppWebhookConfig getModuleConfig(?string $moduleName = NULL)
 */
class AppWebhookBusinessTester extends Actor
{
    use _generated\AppWebhookBusinessTesterActions;
}
