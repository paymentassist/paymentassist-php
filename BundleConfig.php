<?php

namespace PaymentAssist;

use Composer\Script\Event;

/**
 * Class BundleConfigs
 *
 * @package PaymentAssist\ApiClient
 *
 */
class BundleConfig
{
    /**
     * @param Event $event
     */
    public static function publishConfig(Event $event)
    {
        $packageDir = $event->getComposer()->getConfig()->get('vendor-dir') . '/../src/PaymentAssist/config';

        $files = [
            $packageDir . '/apiclient.php'  => __DIR__ . '/../../../config/',
            $packageDir . '/.apiclient.env' => __DIR__ . '/../../../',
        ];

        foreach ($files as $file => $destination) {
            if (!file_exists($destination)) {
                if (strstr($destination, 'config') !== false) {
                    $destination = substr($destination, 0, -7);
                }
            }

            $file        = realpath($file);
            $destination = realpath($destination);

            if (file_exists($destination)) {
                if (copy($file, $destination . DIRECTORY_SEPARATOR . basename($file))) {
                    echo 'Successfully published ' . basename($file) . ' to ' . $destination . "\r\n";
                } else {
                    echo 'Failed puplishing ' . basename($file) . ' to ' . $destination . "\r\n";
                }
            }
        }
    }
}
