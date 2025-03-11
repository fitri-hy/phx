<?php
namespace Src\Components;

use Core\PHXFramework;

class PWA {
    public function index() {
        echo PHXFramework::render("
            [div id:'install-modal' class:'modal']
				[div class:'modal-content']
					[span class:'close']
						[svg width:'20' height:'20' viewBox:'0 0 24 24' fill:'currentColor' xmlns:'http@www.w3.org/2000/svg']
							[path d:'M18 6L6 18' stroke:'black' stroke-width:'2' stroke-linecap:'round' stroke-linejoin:'round'/]
							[path d:'M6 6L18 18' stroke:'black' stroke-width:'2' stroke-linecap:'round' stroke-linejoin:'round'/]
						[/svg]
					[/span]
					[img class:'logo-pwa' width:'80' height:'80' src:'/images/logo/logo.png' alt:'logo']
					[h2]Install Apps[/h2]
					[p]Add this app to your home screen for a better experience..[/p]
					[button id:'install-button']Install Now[/button]
				[/div]
			[/div]
        ");
    }
}
