<?php
/**
 * generates the header.phar archive for production use
 */

chdir(__DIR__);

$phar = new Phar(
    'header.phar',
    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
    'header.phar'
);

$phar->buildFromDirectory('../src', '/\.php$/');

/**
 * @todo index.php
 */

//$phar->addFile('index.php');
//$phar->createDefaultStub('index.php');
