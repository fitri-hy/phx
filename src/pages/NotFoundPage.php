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
			<div class='screen'>
				<div class='main'>
					<img class='logo' width='100' height='100' src='/images/logo/logo.png' alt='logo'>
					<h1 class='title'>404</h1>
					<p class='subtitle'>Page Not Found</p>
					<a class='link' href='/'>Back Home</a>
				</div>
			</div>
        ");
    }
}
