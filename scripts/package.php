#!/usr/bin/env php
<?php

error_reporting(error_reporting() ^ E_DEPRECATED);
if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
    error_reporting(error_reporting() ^ E_DEPRECATED);
}
date_default_timezone_set('America/Chicago');

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

/**
 * Recursively populated $GLOBALS['files']
 *
 * @param string $path The path to glob through.
 *
 * @return void
 * @uses   $GLOBALS['files']
 */
function readDirectory($path)
{
    foreach (glob($path . '/*') as $file) {
        if (!is_dir($file)) {
            $GLOBALS['files'][] = $file;
        } else {
            readDirectory($file);
        }
    }
}

$outsideDir = realpath(dirname(__DIR__));

$version = file_get_contents($outsideDir . '/VERSION');

$api_version     = $version;
$api_state       = 'beta';

$release_version = $version;
$release_state   = 'beta';
$release_notes   = "This is an beta release, see readme.txt for examples.";

$summary     = "A PHP OAuth2 library rev.23";

$description =<<<EOF
This is an beta release, see readme.txt for examples.
EOF;

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
        'filelistgenerator'       => 'file',
        'outputdirectory'         => dirname(__DIR__),
        'simpleoutput'            => true,
        'baseinstalldir'          => '/',
        'packagedirectory'        => $outsideDir,
        'dir_roles'               => array(
            'benchmarks'          => 'doc',
            'examples'            => 'doc',
            'library'             => 'php',
            'library/OAuth2'      => 'php',
            'tests'               => 'test',
        ),
        'exceptions'              => array(
            'CHANGELOG'           => 'doc',
            'readme.md'           => 'doc',
            'VERSION'             => 'doc',
            'LICENSE.txt'         => 'doc',
        ),
        'ignore'                  => array(
            'build/*',
            'package.xml',
            'build.xml',
            'scripts/*',
            '.git',
            '.gitignore',
            'tests/phpunit.xml',
            'tests/build*',
            '.project',
            '.buildpath',
            'releases',
            '.settings',
            'vendor/*',
            '*.tgz'
        )
    )
);

$package->setPackage('OAuth2');
$package->setSummary($summary);
$package->setDescription($description);
$package->setChannel('zircote.github.com/pear');
$package->setPackageType('php');
$package->setLicense(
    'The MIT License',
    'http://www.opensource.org/licenses/MIT'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);
/**
 * Dependencies
 */

$maintainers = array(
    array(
        'name'  => 'Robert Allen',
        'user'  => 'zircote',
        'email' => 'zircote@gmail.com',
        'role'  => 'lead',
    )
);

foreach ($maintainers as $_m) {
    $package->addMaintainer(
        $_m['role'],
        $_m['user'],
        $_m['name'],
        $_m['email']
    );
}

$files = array(); // classes and tests
readDirectory($outsideDir . '/library');
readDirectory($outsideDir . '/tests');

$base = $outsideDir . '/';

foreach ($files as $file) {

    $file = str_replace($base, '', $file);

    $package->addReplacement(
        $file,
        'package-info',
        '@name@',
        'name'
    );

    $package->addReplacement(
        $file,
        'package-info',
        '@package_version@',
        'version'
    );
}

$files = array(); // reset global
readDirectory($outsideDir . '/library');

foreach ($files as $file) {
    $file = str_replace($base, '', $file);
    $package->addInstallAs($file, str_replace('library/', '', $file));
}


$package->setPhpDep('5.3.3');

$package->setPearInstallerDep('1.7.0');
$package->generateContents();
$package->addRelease();


if (   isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}