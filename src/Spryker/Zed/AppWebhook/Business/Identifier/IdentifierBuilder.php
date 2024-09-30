<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AppWebhook\Business\Identifier;

use Ramsey\Uuid\Uuid;

class IdentifierBuilder implements IdentifierBuilderInterface
{
    public function getIdentifier(): string
    {
        return Uuid::uuid4()->toString();
    }
}
