<?php

namespace PaymentAssist\Helpers;

use libphonenumber\{NumberParseException, PhoneNumberUtil};
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
        $str = '';
        foreach ($params as $k => $v) {
            $k = strtoupper($k);
            if ($k !== 'SIGNATURE' && $k !== 'API_KEY') {
                $str .= $k . '=' . $v . '&';
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
     * Converts various values representing "true" or "false" to bool type. Can be used to determine if the value read
     * from an env file is representing true or false. Works with 'true', 'false', '0', '1', 'yes', 'no' etc.
     *
     * @param mixed $val
     * @param bool  $return_null
     *
     * @return bool|mixed
     */
    public function isTrue($val, bool $return_null = false)
    {
        $booleanValue = (is_string($val) ? filter_var(
            $val,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        ) : (bool)$val);

        return ($booleanValue === null && !$return_null ? false : $booleanValue);
    }

    /**
     * Returns a normalised telephone number for DB identity lookup
     *
     * @param string      $phoneNumber
     * @param string|null $regionCode
     *
     * @return bool
     */
    public static function isValidPhoneNumber(string $phoneNumber, string $regionCode = null): bool
    {
        if (empty($phoneNumber)) {
            return false;
        }

        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $countryCallingCode = $phoneNumberUtil->getCountryCodeForRegion(strtoupper($regionCode));
        if ($countryCallingCode === 0) {
            $countryCallingCode = 44;
        }
        $regionCode        = $phoneNumberUtil->getRegionCodeForCountryCode($countryCallingCode);
        $phoneNumberObject = $phoneNumberUtil->getExampleNumber($regionCode);

        $validNumber = true;
        try {
            $phoneNumberObject = $phoneNumberUtil->parse($phoneNumber, $regionCode);
        } catch (NumberParseException $e) {
            $validNumber = false;
        }

        return $validNumber && $phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, $regionCode);
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
