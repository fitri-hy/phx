<?php
namespace Src;

use Core\PHXFramework;
use Core\PHXController;

class App extends PHXController {
    public static function App(string $Children) {
        $props = self::Props();

        echo PHXFramework::render("
			<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>{$props['meta_title']}</title>
                    <meta name='description' content='{$props['meta_description']}'>
                    <link rel='icon' href='/images/logo/favicon.ico'>
                    <link rel='stylesheet' href='/css/style.css'>
                    <link rel='manifest' href='/manifest.json'>
                </head>
                <body>
                    <main class='main-container'>
                        $Children
                    </main>
					[[PWA]]
                    <script src='/js/app.js'></script>
                </body>
			</html>
        ");
    }
}
