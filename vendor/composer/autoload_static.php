<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit398775f01004b0ea5d0930a98d40117b
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
            'think\\' => 6,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'think\\' => 
        array (
            0 => __DIR__ . '/../..' . '/thinkphp/library/think',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit398775f01004b0ea5d0930a98d40117b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit398775f01004b0ea5d0930a98d40117b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
