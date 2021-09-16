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
        $packageDir = $event->getComposer()->getConfig()->get('vendor-dir');

        if (!file_exists($packageDir)) {
            if (substr_count($packageDir, 'vendor') == 2) {
                $packageDir = substr_replace($packageDir, '', strrpos($packageDir, 'vendor'));
            }
        } else {
            $packageDir .= '/../';
        }
        $packageDir .= 'src/PaymentAssist/config';

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
                    $message = 'Successfully published ';
                } else {
                    $message = 'Failed to publish ';
                }
                echo $message . basename($file) . ' to ' . $destination . "\r\n";
            }
        }
    }
}
