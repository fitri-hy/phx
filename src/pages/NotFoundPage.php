<?php
namespace Src\Pages;

use Core\PHXController;

class NotFoundPage {
    public function index() {
        PHXController::render("
            [h1]
                404 Not Found
            [/h1]
            [p]
                Halaman yang Anda cari tidak tersedia.
            [/p]
        ");
    }
}
