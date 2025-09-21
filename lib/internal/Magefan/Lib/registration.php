<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

/**
 * Class Magefan Lib Autoload
 */
class Magefan_Lib_Autoload
{
    /**
     * @var null
     */
    static $prefixes = null;

    /**
     * @param $class
     */
    public static function autoload($class)
    {
        $baseDir = __DIR__ . '/';
        $ds = DIRECTORY_SEPARATOR;

        if (null === self::$prefixes) {
            self::$prefixes = [];
            $folders = glob($baseDir . $ds . '*', GLOB_NOSORT);
            foreach ($folders as $folder) {
                $folder = str_replace($baseDir . $ds, '', $folder);
                if (false !== strpos($folder, '.')) {
                    continue;
                }
                self::$prefixes[] = $folder;
            }
        }

        foreach (self::$prefixes as $prefix) {
            if (strpos($class, $prefix) !== 0) {
                continue;
            }
            $file = $baseDir . $ds .  str_replace('\\', $ds, $class) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}
spl_autoload_register(['Magefan_Lib_Autoload', 'autoload']);
