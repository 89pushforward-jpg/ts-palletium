<?php
require_once __DIR__ . '/lib-admin.php';
session_destroy();
header('Location: login.php');
