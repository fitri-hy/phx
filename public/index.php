<?php
use Core\PHXRouter;
use Core\PHXLogger;
// use Core\PHXDatabase;

session_start();

require '../vendor/autoload.php';
require '../routes/web.php';
require '../routes/api.php';

PHXLogger::Logger();
// PHXDatabase::getInstance();
PHXRouter::Route();
