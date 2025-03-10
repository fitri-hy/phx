<?php
use Core\PHXRouter;
use Src\Pages\HomePage;

PHXRouter::get('/', HomePage::class, 'index');
