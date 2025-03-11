<?php
namespace Src\Pages;

use Core\PHXController;
use Src\App;

class HomePage {
    public function index() {
        App::Props([
			'meta_title' => 'Welcome to PHX Framework!',
			'meta_description' => 'Explore Now & Create Your App with PHXFramework!'
		]);

        PHXController::render("
			[div class:'screen']
				[div class:'main']
					[img class:'logo' width:'100' height:'100' src:'/images/logo/logo.png' alt:'logo']
					[h1 class:'title']PHX Framework[/h1]
					[p class:'subtitle']Fast | Secure | Powerful[/p]
					[a class:'link' href:'https://github.com/fitri-hy/phx' target:'_blank']Github[/a]
				[/div]
			[/div]
        ");
    }
}
