<?php
/**
 * generates the headerphp.phar archive for production use
 */

chdir(__DIR__);

$phar = new Phar(
    'headerphp.phar',
    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
    'headerphp.phar'
);
$phar->buildFromDirectory('../src/htlwy', '/\.php$/');
