<?php
use Core\PHXRouter;
use Src\Api\Welcome;

PHXRouter::get('/v1', Welcome::class, 'index');
