<?php

/**
 * This configuration is used for TESTING only and will never be used in production!
 */

use Spryker\Shared\Application\Log\Config\SprykerLoggerConfig;
use Spryker\Shared\ErrorHandler\ErrorHandlerConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Log\LogConstants;

$config[KernelConstants::PROJECT_NAMESPACE] = 'Spryker';
$config[KernelConstants::PROJECT_NAMESPACES] = ['Spryker'];
$config[KernelConstants::CORE_NAMESPACES] = ['Spryker'];
$config[KernelConstants::ENABLE_CONTAINER_OVERRIDING] = true;
$config[ErrorHandlerConstants::ERROR_LEVEL] = E_ALL & ~E_DEPRECATED;
$config[LogConstants::LOGGER_CONFIG] = SprykerLoggerConfig::class;
$config[LogConstants::LOG_FILE_PATH] = sys_get_temp_dir() . '/logs';
