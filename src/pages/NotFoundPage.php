<?php
namespace Src\Pages;

use Core\PHXController;
use Src\App;

class NotFoundPage {
    public function index() {
        App::Props([
			'meta_title' => '404 Page Not Found',
			'meta_description' => 'Sorry, the page you are looking for is not available or may have moved.'
		]);
		
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
