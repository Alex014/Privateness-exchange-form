<?php

use lib\Script;

require '../lib/Script.php';


ini_set('display_errors', true);
error_reporting(E_ERROR);

$script = new Script();
$script->parseTokens();