<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Core\Auth;

Auth::logout();
header('Location: /login.php');
exit;
