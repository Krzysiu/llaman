<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite34197b43fdaa739bc8b87618a22e0cb
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GetOptionKit\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GetOptionKit\\' => 
        array (
            0 => __DIR__ . '/..' . '/corneltek/getoptionkit/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite34197b43fdaa739bc8b87618a22e0cb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite34197b43fdaa739bc8b87618a22e0cb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite34197b43fdaa739bc8b87618a22e0cb::$classMap;

        }, null, ClassLoader::class);
    }
}
