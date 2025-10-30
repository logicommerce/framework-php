<?php

namespace FWK\Core\Resources;

use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Resources\Date;
use SDK\Core\Dtos\Factories\PluginPropertiesFactory;
use SDK\Core\Resources\Redis;
use SDK\Enums\RedisKey;

/**
 * This is the LcFWK class.
 * The API LcFWK will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see LcFWK::getUseCacheRedisSession()
 *
 *
 * @package FWK\Core\Resources
 */
abstract class LcFWK extends Element {
    use ElementTrait;

    private const MODULE = 'com.logicommerce.phpcommerce';

    private static string $useCacheRedisObject = 'true';

    private static string $useCacheRedisSession = 'true';

    private static string $logEnabled = 'true';

    private static string $logHandler = 'syslog';

    private static string $logLevel = 'INFO';

    private static string $loggerLevel = '1';

    private static string $loggerConnectionEnabled = 'true';

    private static string $loggerRequestHandlerEnabled = 'true';

    private static string $loggerExceptionEnabled = 'true';

    private static string $loggerDebugInfoEnabled = '';

    private static string $loggerHealthcheckEnabled = '';

    private static string $loggerTimerEnabled = '';

    private static string $lifeTimeCacheObjects = '300';

    private static string $lifeTimeCacheObjectApplication = '300';

    private static string $lifeTimeCacheObjectLcFWK = '300';

    private static string $lifeTimeCacheObjectPlugins = '300';

    private static string $lifeTimeSession = '300';

    private static string $maintenance = 'false';

    private static string $maintenanceAllowIps = '';

    private static string $errorOnCacheableZeroTTL = 'false';

    private static string $twigOptionAutoreload = 'true';

    private static string $twigOptionCache = 'true';

    private static string $twigOptionOptimizations = 'true';

    private static string $twigOptionStrictVariables = 'false';

    private static string $loginRequired = 'false';

    private static string $twigDevel = 'false';

    private static string $cleanCachePath = '';

    private static string $phpCommerceToken = '';

    /**
     * Start LcFWK object
     * Initialize the object LcFWK and creates the constants needed to use the SDK
     *
     * @return void
     */
    public static function start(): void {
        $key = RedisKey::OBJECT . ':' . self::class;
        $cacheCommercePhp = null;
        if (Redis::isEnabled()) {
            $cacheCommercePhp = Redis::get($key);
        }
        if (is_null($cacheCommercePhp)) {
            $commercePhp = Loader::service(Services::PLUGIN)->getPluginPropertiesByModule(self::MODULE);
        } else {
            $commercePhp = PluginPropertiesFactory::getPluginProperties(json_decode($cacheCommercePhp, true));
        }
        if (!is_null($commercePhp) && is_null($commercePhp->getError())) {
            foreach ($commercePhp->getProperties() as $property) {
                if (property_exists(self::class, $property->getName())) {
                    self::${$property->getName()} = $property->getValue();
                }
            }
            if (is_null($cacheCommercePhp)) {
                if (Redis::isEnabled() && self::getUseCacheRedisObject()) { // avoid use cache
                    Redis::set($key, $commercePhp, self::getLifeTimeCacheLcFWK());
                }
            }
        }
        self::setSDKConstants();
    }


    /**
     * Delete the saved cache objects
     */
    public static function deleteRedisCacheObjecs(): void {
        Redis::getKeys(RedisKey::OBJECT);
        foreach (Redis::getKeys(RedisKey::OBJECT) as $key) {
            Redis::delete($key);
        }
    }

    private static function setSDKConstants() {
        define('LOG_ENABLED', self::getLogEnabled());
        define('LOG_HANDLER', self::getLogHandler());
        define('LOG_LEVEL', self::getLogLevel());

        define('LOGGER_LEVEL', self::getLoggerLevel());
        define('CONNECTIONLOGGER_ENABLED', self::getLoggerConnectionEnabled());
        define('REQUESTHANDLERLOGGER_ENABLED', self::getLoggerRequestHandlerEnabled());
        define('EXCEPTIONLOGGER_ENABLED', self::getLoggerExceptionEnabled());

        define('DEBUGINFOLOGGER_ENABLED', self::getLoggerDebugInfoEnabled());
        define('HEALTHCHECKLOGGER_ENABLED', self::getLoggerHealthcheckEnabled());
        define('TIMERLOGGER_ENABLED', self::getLoggerTimerEnabled());

        define('LIFE_TIME_CACHE_OBJECTS', self::getLifeTimeCacheObjects());
        define('LIFE_TIME_CACHE_APPLICATION', self::getLifeTimeCacheApplication());
        define('LIFE_TIME_CACHE_PLUGINS', self::getLifeTimeCachePlugins());

        define('LIFE_TIME_SESSION', self::getLifeTimeSession());
        define('LIFE_TIME_SESSION_BOT', 5);

        define('ERROR_ON_CACHEABLE_ZERO_TTL', self::getErrorOnCacheableZeroTTL());

        define('USE_CACHE_REDIS_OBJECT', self::getUseCacheRedisObject());
    }

    /**
     * This method returns the useCacheRedisSession value.
     *
     * @return bool
     */
    public static function getUseCacheRedisSession(): bool {
        return self::$useCacheRedisSession === 'true' ? true : false;
    }

    /**
     * This method returns the useCacheRedisObject value.
     *
     * @return bool
     */
    public static function getUseCacheRedisObject(): bool {
        return self::$useCacheRedisObject === 'true' ? true : false;
    }

    /**
     * This method returns the logEnabled value.
     *
     * @return bool
     */
    public static function getLogEnabled(): bool {
        return self::$logEnabled === 'true' ? true : false;
    }

    /**
     * This method returns the logHandler value.
     *
     * @return string
     */
    public static function getLogHandler(): string {
        return self::$logHandler;
    }

    /**
     * This method returns the logLevel value.
     *
     * @return string
     */
    public static function getLogLevel(): string {
        return self::$logLevel;
    }

    /**
     * This method returns the loggerLevel value.
     *
     * @return int
     */
    public static function getLoggerLevel(): int {
        return intval(self::$loggerLevel);
    }

    /**
     * This method returns the loggerConnectionEnabled value.
     *
     * @return bool
     */
    public static function getLoggerConnectionEnabled(): bool {
        return self::$loggerConnectionEnabled === 'true' ? true : false;
    }

    /**
     * This method returns the loggerRequestHandlerEnabled value.
     *
     * @return bool
     */
    public static function getLoggerRequestHandlerEnabled(): bool {
        return self::$loggerRequestHandlerEnabled === 'true' ? true : false;
    }

    /**
     * This method returns the loggerExceptionEnabled value.
     *
     * @return bool
     */
    public static function getLoggerExceptionEnabled(): bool {
        return self::$loggerExceptionEnabled === 'true' ? true : false;
    }

    /**
     * This method returns the loggerDebugInfoEnabled value.
     *
     * @return bool
     */
    public static function getLoggerDebugInfoEnabled(): bool {
        $result = false;
        if (strlen(self::$loggerDebugInfoEnabled)) {
            $date = Date::create(self::$loggerDebugInfoEnabled);
            if (!is_null($date)) {
                $result = (new \DateTime()) < ($date->getDateTime());
            }
        }
        return $result;
    }

    /**
     * This method returns the loggerHealthcheckEnabled value.
     *
     * @return bool
     */
    public static function getLoggerHealthcheckEnabled(): bool {
        $result = false;
        if (strlen(self::$loggerHealthcheckEnabled)) {
            $date = Date::create(self::$loggerHealthcheckEnabled);
            if (!is_null($date)) {
                $result = (new \DateTime()) < ($date->getDateTime());
            }
        }
        return $result;
    }

    /**
     * This method returns the loggerTimerEnabled value.
     *
     * @return bool
     */
    public static function getLoggerTimerEnabled(): bool {
        $result = false;
        if (strlen(self::$loggerDebugInfoEnabled)) {
            $date = Date::create(self::$loggerDebugInfoEnabled);
            if (!is_null($date)) {
                $result = (new \DateTime()) < ($date->getDateTime());
            }
        }
        return $result;
    }

    /**
     * This method returns the errorOnCacheableZeroTTL value.
     *
     * @return bool
     */
    public static function getErrorOnCacheableZeroTTL(): bool {
        return self::$errorOnCacheableZeroTTL === 'true' ? true : false;
    }

    /**
     * This method returns the lifeTimeSession value.
     *
     * @return int
     */
    public static function getLifeTimeSession(): int {
        return (3600 * 2) + 300;
    }

    /**
     * This method returns the lifeTimeCacheObjects value.
     *
     * @return int
     */
    public static function getLifeTimeCacheObjects(): int {
        return intval(self::$lifeTimeCacheObjects);
    }

    /**
     * This method returns the lifeTimeCacheObjectApplication value.
     *
     * @return int
     */
    public static function getLifeTimeCacheApplication(): int {
        return intval(self::$lifeTimeCacheObjectApplication);
    }

    /**
     * This method returns the lifeTimeCacheObjectLcFWK value.
     *
     * @return int
     */
    public static function getLifeTimeCacheLcFWK(): int {
        return intval(self::$lifeTimeCacheObjectLcFWK);
    }

    /**
     * This method returns the lifeTimeCacheObjectPlugins value.
     *
     * @return int
     */
    public static function getLifeTimeCachePlugins(): int {
        return intval(self::$lifeTimeCacheObjectPlugins);
    }

    /**
     * This method returns the twigOptionAutoreload value.
     *
     * @return bool
     */
    public static function getTwigOptionAutoreload(): bool {
        return self::$twigOptionAutoreload === 'true' ? true : false;
    }

    /**
     * This method returns the twigOptionCache value.
     *
     * @return bool
     */
    public static function getTwigOptionCache(): bool {
        return self::$twigOptionCache === 'true' ? true : false;
    }

    /**
     * This method returns the twigOptionOptimizations value.
     *
     * @return bool
     */
    public static function getTwigOptionOptimizations(): bool {
        return self::$twigOptionOptimizations === 'true' ? true : false;
    }

    /**
     * This method returns the twigOptionStrictVariables value.
     *
     * @return bool
     */
    public static function getTwigOptionStrictVariables(): bool {
        return self::$twigOptionStrictVariables === 'true' ? true : false;
    }

    /**
     * This method returns the loginRequired value.
     *
     * @return bool
     */
    public static function getLoginRequired(): bool {
        return self::$loginRequired === 'true' ? true : false;
    }

    /**
     * This method returns the twigDevel value.
     *
     * @return bool
     */
    public static function getTwigDevel(): bool {
        return self::$twigDevel === 'true' ? true : false;
    }

    /**
     * This method returns the maintenance value.
     *
     * @return bool
     */
    public static function getMaintenance(): bool {
        return self::$maintenance === 'true' ? true : false;
    }

    /**
     * This method returns the maintenanceAllowIps value.
     *
     * @return array
     */
    public static function getMaintenanceAllowIps(): array {
        return explode(',', self::$maintenanceAllowIps);
    }

    /**
     * This method returns the phpCommerceToken value.
     *
     * @return string
     */
    public static function getPhpCommerceToken(): string {
        return self::$phpCommerceToken;
    }

    /**
     * This method returns the cleanCachePath value.
     *
     * @return string
     */
    public static function getCleanCachePath(): string {
        return self::$cleanCachePath;
    }
}
