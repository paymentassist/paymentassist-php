<?php

namespace PaymentAssist\Helpers;

use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Helpers
 *
 * @package PaymentAssist\ApiClient
 */
class Helpers
{
    /**
     * Generates authentication signature from supplied params
     *
     * @param array $params
     * @param array $credentials
     *
     * @return string
     */
    public static function generateSignature(array $params, array $credentials): string
    {
        ksort($params);
        $params = array_change_key_case($params, CASE_UPPER);

        $str = '';
        foreach ($params as $k => $v) {
          if (!in_array($k, ['SIGNATURE', 'API_KEY'])) {
            $str .= sprintf('%s=%s&', $k, $v);
          }
        }

        return hash_hmac('sha256', $str, $credentials['secret'], false);
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public static function camel2snake(string $input): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
    }

    /**
     * @param string $dir
     * @param string $file
     *
     * @return string|null
     */
    public static function searchFile(string $dir, string $file): ?string
    {
        $filter = function ($current, $key, $iterator) use ($file) {
            if ($iterator->hasChildren()) {
                return true;
            }
            if ($current->isFile()
                && basename($key) == $file
                && substr_count($current->getPathname(), 'vendor') == 1) {
                return true;
            }

            return false;
        };

        /** @var callable $filter */
        $files = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($dir), $filter
            )
        );
        foreach ($files as $file) {
            return $file->getRealpath();
        }

        return null;
    }
}
