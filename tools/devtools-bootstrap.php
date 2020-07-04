<?php

require __DIR__ . '/../app/bootstrap.php';

if (!in_array('phar', stream_get_wrappers()) && extension_loaded('phar')) {
    stream_wrapper_restore('phar');
}

// if DI is not compiled then we need the object manager to generate classes for us
if (!file_exists(__DIR__ . '/../generated/metadata/global.php')) {
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
    $bootstrap->getObjectManager();
}
