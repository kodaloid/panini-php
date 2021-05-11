<?php



namespace App;



if (!defined('__ROOT__')) die('Access denied');



/**
 * The primary class for the engine. Implements a singleton pattern so
 * that the global state/registry can be accessed from anywhere.
 */
final class Helper {



    /**
     * Alternative to the implode method that adds a max limiter.
     */
    static function implode_path($glue = '/', $pieces, $max_count = 10) {
        // make sure we always have an array.
        $pieces = is_array($pieces) ? $pieces : [];
        // trim the array if it exceeds our limits.
        if (count($pieces) > $max_count) {
            $pieces = array_slice($pieces, 0, $max_count);
        }
        $pieces = array_filter($pieces);
        return implode($glue, $pieces);
    }



    /**
     * Convert any string into a url friendly slug.
     */
    static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) return 'n-a';
        return $text;
    }



    /**
     * Global function for converting a relative URL to an absolute one.
     */
    static function absolute_url($relative_path) {
        return rtrim(SITE_URL, '/') . '/' . ltrim($relative_path, '/');
    }

    
    
    /**
     * Require all PHP files within a specific path.
     * @param string $path The absolute path to the file or directory to require.
     */
    static function require_any($path) {
        if (is_dir($path)) {
            //$files = array_slice(scandir($path), 2);
            $files = glob("{$path}/*.php");
            foreach ($files as $file) {
                require_once $file;
            }
        }
        else if (is_file($path)) {
            require_once $path;
        }
    }
}