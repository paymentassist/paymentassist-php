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
            copy($file, $destination . basename($file));
        }
    }
}
