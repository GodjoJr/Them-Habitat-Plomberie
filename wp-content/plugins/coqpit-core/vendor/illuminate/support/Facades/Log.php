<?php
/**
 * @license MIT
 *
 * Modified by COQPIT on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace COQPIT\Core\Vendor\Illuminate\Support\Facades;

/**
 * @method static \Psr\Log\LoggerInterface build(array $config)
 * @method static \Psr\Log\LoggerInterface stack(array $channels, string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface channel(string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface driver(string|null $driver = null)
 * @method static \Illuminate\Log\LogManager shareContext(array $context)
 * @method static array sharedContext()
 * @method static \Illuminate\Log\LogManager withoutContext()
 * @method static \Illuminate\Log\LogManager flushSharedContext()
 * @method static string|null getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static \Illuminate\Log\LogManager extend(string $driver, \Closure $callback)
 * @method static void forgetChannel(string|null $driver = null)
 * @method static array getChannels()
 * @method static void emergency(string|\COQPIT_Core_VendorStringableStringable\Stringable $message, array $context = [])
 * @method static void critical(string|\COQPIT_Core_VendorStringableStringable\Stringable $message, array $context = [])
 * @method static void warning(string|\COQPIT_Core_VendorStringableStringable\Stringable $message, array $context = [])
 * @method static void info(string|\COQPIT_Core_VendorStringableStringable\Stringable $message, array $context = [])
 * @method static void log(mixed $level, string|\COQPIT_Core_VendorStringableStringable\Illuminate\Contracts\Support\Arrayable|\COQPIT\Core\Vendor\Illuminate\Contracts\Support\Jsonable|\COQPIT\Core\Vendor\Illuminate\Support\Stringable|array|string $message, array $context = [])
 * @method static \Illuminate\Log\Logger withContext(array $context = [])
 * @method static void listen(\Closure $callback)
 * @method static \Psr\Log\LoggerInterface getLogger()
 * @method static \COQPIT\Core\Vendor\Illuminate\Contracts\Events\Dispatcher getEventDispatcher()
 * @method static void setEventDispatcher(\COQPIT\Core\Vendor\Illuminate\Contracts\Events\Dispatcher $dispatcher)
 * @method static \Illuminate\Log\Logger|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Log\Logger|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 *
 * @see \Illuminate\Log\LogManager
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
