<?php

foreach ($_GET as $param => $value) {
    $token = $param;
    break;
}

if (isset($token)) {
    echo $token;
}