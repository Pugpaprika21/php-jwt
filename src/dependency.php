<?php

/**
 * @author PUG <pugpaprika21@gmail.com>
 * @last update 10-10-2566
 */

use Illuminate\Database\Capsule\Manager as Capsule;

#######################################################################################################

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Bangkok');

$GLOBALS['APP_ENV'] = parse_ini_file(__DIR__ . "../../config.env");

#######################################################################################################

# constant

define('CREATE_DATE_AT', now('d'));
define('CREATE_TIME_AT', now('t'));
define('CREATE_DT_AT', now());
define('U_SYS_TOKEN', token_generator(rend_string() . CREATE_TIME_AT));

#######################################################################################################

# {eloquent}

$configDB = require_once __DIR__ . '../../config/database.php';

$capsule = new Capsule;
$capsule->addConnection($configDB['connection']['eloquent']);
$capsule->bootEloquent();
$capsule->setAsGlobal();

# {Redbean}

if (!empty($GLOBALS['APP_ENV']['DB_NAME'])) {

    require_once __DIR__ . "../../src/classes/rb.php";

    R::setup($GLOBALS['APP_ENV']['DB_CONNECT_DNS'], $GLOBALS['APP_ENV']['DB_USERNAME'], '');
    R::debug(false);
}

#######################################################################################################

# middleware

$cors = require_once __DIR__ . '../../middleware/cors.php';
$session = require_once __DIR__ . '../../middleware/session.php';
$serveStatic = require_once __DIR__ . '../../middleware/public.php';

#######################################################################################################
