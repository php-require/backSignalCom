<?php

namespace App\Service;

class Validation
{
    public const REQUEST_ID = '/^[A-Za-z0-9]+$/';  
    public const FULL_NAME_RE = '/^[а-яё. -]+$/iu';
    public const EMAIL = '/^[\w-]+\.[a-zA-Z]+$/';
    public const CONTACT_CONSTRAINTS = [
        // 'id' => 'is_int',
        'email' => [self::class, 'isEmail'],
        'name' => [self::class, 'isFullName'],
        'organization' => [self::class, 'isOrganization'],
        'phone' => 'is_string',
        'certreq' => 'is_string',
        'sig' => 'is_string',
        'doc' => 'is_string',
        'verification' => 'is_string',
    ];


    public static function checkArray(array $array, array $constraints): bool
    {
        foreach ($constraints as $key => $checker) {
            if (!array_key_exists($key, $array) || !$checker($array[$key])) {
                return false;
            }
        }
        return empty(array_diff_key($constraints, $array));
    }


    public static function isEmail($value): bool
    {
        return is_string($value) &&
        filter_var($value, FILTER_VALIDATE_EMAIL);
    }  


    public static function isFullName($value): bool
    {
        return is_string($value) &&
            preg_match(self::FULL_NAME_RE, $value);
    }


    public static function isOrganization($value): bool
    {
        return is_string($value);
    }

   
    public static function isRequestId($value): bool
    {
        return is_string($value) &&
            preg_match(self::REQUEST_ID, $value);
    }
 
 
    public static function isFile($value): bool
    {
        // TODO(jury): Add File check
        $value   = strtolower($value);  
        $ext = array("jpg", "jpeg", "png", "pdf","tiff","bmp","djvu","pcx");
        return in_array($value, $ext);
    }

}
