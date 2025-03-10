<?php
use Core\PHXRouter;
use Core\PHXDatabase;

session_start();

require '../vendor/autoload.php';
require '../routes/web.php';
require '../routes/api.php';

PHXDatabase::getInstance();
PHXRouter::Route();
