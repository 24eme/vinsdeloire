<?php
class CacheFunction {

    const CLASS_CACHE = "sfFileCache";
    const AUTOMATIC_CLEANING_FACTOR = 0;

    public static function cache($location, $callable, $arguments = array(), $lifetime = 86400) {
      if (sfConfig::get('sf_debug')) {
          return call_user_func_array($callable, $arguments);
      }

      $function_cache = new sfFunctionCache(self::getCache($location, $lifetime));

      return $function_cache->call($callable, $arguments);
    }

    public static function remove($location, $lifetime = 86400) {
      $cache = self::getCache($location, $lifetime);
      $cache->clean();
    }

    public static function getCache($location, $lifetime) {
      $class_cache = self::CLASS_CACHE;
      $cache_dir = sfConfig::get('sf_app_cache_dir').DIRECTORY_SEPARATOR.$location;

      return new $class_cache(array('cache_dir' => $cache_dir,
                                   'lifetime' => $lifetime,
                                   'automatic_cleaning_factor' => self::AUTOMATIC_CLEANING_FACTOR));
    }
}
