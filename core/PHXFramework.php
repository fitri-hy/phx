<?php
namespace Core;

use Dotenv\Dotenv;

class PHXFramework {
    public static function render($Children) {
		if (!isset($_ENV['USE_MINIFY'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }
		$useMinify = filter_var($_ENV['USE_MINIFY'], FILTER_VALIDATE_BOOLEAN);
		$usePHXEngine = filter_var($_ENV['USE_PHX_ENGINE'], FILTER_VALIDATE_BOOLEAN);
		if (!$usePHXEngine) {
            return trim($Children);
        }
		
        $render = trim($Children);
        $render = preg_replace('/\[!PHX html\]/', '<!DOCTYPE html>', $render);
        $render = preg_replace('/<!--.*?-->/', '', $render);
        $render = self::processComponents($render);
        $render = self::convertPHXTags($render);
        $render = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/s', function($matches) {
            return "<style>" . trim($matches[1]) . "</style>";
        }, $render);
        $render = self::replaceUrls($render);
		
		if ($useMinify) {
            $render = self::minimizeHTML($render);
        }
        
        return $render;
    }

    private static function replaceUrls($content) {
        $content = preg_replace_callback('/(https?|http)@([a-zA-Z0-9.-]+)(\/[^\s]*)?/', function($matches) {
            $url = $matches[1] . '://' . $matches[2];
            return $url . (isset($matches[3]) ? $matches[3] : '');
        }, $content);
    
        $content = preg_replace_callback('/(src|href|background|action|data|poster|url)\s*=\s*["\'](https?|http)@([a-zA-Z0-9.-]+)(\/[^\s]*)?["\']/', function($matches) {
            $url = $matches[2] . '://' . $matches[3];
            return $matches[1] . '="' . $url . (isset($matches[4]) ? $matches[4] : '') . '"';
        }, $content);
		
		$content = preg_replace_callback('/class\s*=\s*["\']([^"\']+)["\']/', function($matches) {
			$replacements = [
				'?' => ':',
				'<' => '[',
				'>' => ']'
			];
			$classFixed = strtr($matches[1], $replacements);
			return 'class="' . $classFixed . '"';
		}, $content);

    
        return $content;
    }
    

    private static function processComponents($content) {
        return preg_replace_callback('/\[\[([A-Z][a-zA-Z0-9]+)(.*?)\]\]/s', function ($matches) {
            $componentName = $matches[1];
            $props = trim($matches[2]);

            $className = "Src\\Components\\$componentName";
            if (class_exists($className)) {
                $component = new $className();
                if (method_exists($component, 'index')) {
                    ob_start();
                    $component->index(self::parsePropsToArray($props));
                    return ob_get_clean();
                }
            }
            return "<!-- Component $componentName not found -->";
        }, $content);
    }

    private static function convertPHXTags($content) {
        return preg_replace_callback('/\[([^\]]+)\]/', function ($matches) {
            $tag = $matches[1];

            if ($tag[0] !== '/') {
                $tag = strtr($tag, [':' => '=']);
                $tag = self::convertAttributes($tag);
                return "<$tag>";
            } 
            return "</" . substr($tag, 1) . ">";
        }, $content);
    }

    private static function convertAttributes($tag) {
        return preg_replace_callback('/(\w+=)\'((?:[^\']|(?<=\\\\)\')*)\'/', function ($attrMatches) {
            $value = $attrMatches[2];

            if (preg_match('/\b(alert|console\.log|confirm|prompt|eval|setTimeout|setInterval|function)\s*\(/', $value) ||
                preg_match('/(;|:)\s*[^;]+;/', $value)
            ) {
                return $attrMatches[0];
            }

            return $attrMatches[1] . '"' . $value . '"';
        }, $tag);
    }

    private static function parsePropsToArray($props) {
        preg_match_all('/(\w+)=["\']([^"\']+)["\']/', $props, $matches, PREG_SET_ORDER);
        $propsArray = [];
        foreach ($matches as $match) {
            $propsArray[$match[1]] = $match[2];
        }
        return $propsArray;
    }

    private static function minimizeHTML($html) {
        return preg_replace(['/\s{2,}/', '/>\s+</'], [' ', '><'], trim($html));
    }
}
