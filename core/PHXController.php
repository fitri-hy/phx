<?php
namespace Core;

use Dotenv\Dotenv;

class PHXController {
    protected static array $props = [
        'meta_title' => 'Welcome to PHX Framework!',
        'meta_description' => 'Explore Now & Create Your App with PHXFramework!',
        'base_url' => ''
    ];

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        self::$props['base_url'] = $_ENV['BASE_URL'] ?? 'http://localhost';
    }

    public static function render($PHX) {
        return PHXLayout::render($PHX);
    }

    public static function Props(array $props = null): array {
        if ($props) {
            self::$props = array_merge(self::$props, $props);
        }
        return self::$props;
    }
}
