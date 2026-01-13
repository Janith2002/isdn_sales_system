<?php
require_once __DIR__ . '/../app/config/db.php';

session_unset();
session_destroy();

header("Location: /public/login.php");
exit;
