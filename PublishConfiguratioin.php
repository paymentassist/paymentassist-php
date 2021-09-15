<?php

use Composer\Script\CommandEvent;

class ScriptHandler
{
    public function bundleConfigs(CommandEvent $event)
    {
        $homeDir = $event->getComposer()->getConfig()->get('home');

        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $files = glob($vendorDir, '/*Module/config/*.ini');

        foreach ($files as $file) {
            copy($file, $homeDir.'/config/ini/'.basename($file));
        }
    }
}
