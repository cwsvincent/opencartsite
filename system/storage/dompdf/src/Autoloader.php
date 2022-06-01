<?php
namespace Dompdf;

/**
 * Autoloads Dompdf classes
 *
 * @package Dompdf
 */
class Autoloader
{
    const PREFIX = 'Dompdf';

    /**
     * Register the autoloader
     */
    public static function register()
    {
        spl_autoload_register([new self, 'autoload']);
    }

    /**
     * Autoloader
     *
     * @param string
     */
    public static function autoload($class)
    {
        if ($class === 'Dompdf\Cpdf') {
            require_once DIR_DOMPDF . "/../lib/Cpdf.php";
            return;
        }

        $prefixLength = strlen(self::PREFIX);
        if (0 === strncmp(self::PREFIX, $class, $prefixLength)) {
            $file = str_replace('\\', '/', substr($class, $prefixLength));
            $file = realpath(DIR_DOMPDF . (empty($file) ? '' : '/') . $file . '.php');
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}