<?php
/**
 * --------------------------------------------------------------------------
 * SECURE LOGOUT PROCESS
 * --------------------------------------------------------------------------
 */

if (file_exists('classes/Auth.php')) {
    require_once 'classes/Auth.php';
} elseif (file_exists('../classes/Auth.php')) {
    require_once '../classes/Auth.php';
}

$auth = new Auth();
$auth->logout();
?>