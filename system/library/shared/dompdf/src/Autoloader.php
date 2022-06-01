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
        echo "Autoload.php in src1 <br />";
        if ($class === 'Dompdf\Cpdf') {
            require_once DIR_DOMPDF . "/lib/Cpdf.php";
            echo "Autoload.php in src2 <br />";
            return;
        }
        echo "Autoload.php in src3 <br />";
        $prefixLength = strlen(self::PREFIX);
        if (0 === strncmp(self::PREFIX, $class, $prefixLength)) {
            $file = str_replace('\\', '/', substr($class, $prefixLength));
            $file = realpath(DIR_DOMPDF . (empty($file) ? '' : '/') . $file . '.php');
            if (file_exists($file)) {
                require_once $file;
                echo "Autoload.php in src4 <br />";
            }
        }
        echo "Autoload.php in src5 <br />";
    }
}
