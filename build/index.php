<?php

define('HEADERPHP', 'phar://header.phar/htlwy/headerphp/');

require_once HEADERPHP.'nav.php';
require_once HEADERPHP.'navroot.php';
require_once HEADERPHP.'option.php';


define('OPTION', HEADERPHP.'option/');

require_once OPTION.'author.php';
require_once OPTION.'cache.php';
require_once OPTION.'csp.php';
require_once OPTION.'description.php';
require_once OPTION.'design.php';
require_once OPTION.'headinclude.php';
require_once OPTION.'keywords.php';
require_once OPTION.'manifest.php';
require_once OPTION.'target.php';
require_once OPTION.'title.php';
require_once OPTION.'titlebar.php';
require_once OPTION.'visible.php';

