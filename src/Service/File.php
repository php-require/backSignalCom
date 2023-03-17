<?php

namespace App\Service;


class File
{
    /**
     * Recursively remove a filesystem path
     * @param string $path
     * @return bool
     */
    public static function rrm(string $path): bool
    {
        if (is_file($path) || is_link($path)) {
            return unlink($path);
        }
        // dir
        if (!is_dir($path)) {
            return false;
        }
        foreach (array_diff(scandir($path), ['.', '..']) as $file) {
            self::rrm($path.'/'.$file);
        }

        return rmdir($path);
    }
}
