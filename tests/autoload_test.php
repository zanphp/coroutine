<?php


require __DIR__ . "/../../exception/vendor/autoload.php";

$file = __DIR__ . "/../vendor/autoload.php";

if (!file_exists($file)) {
    echo "\033[1;31m$file not found\033[0m\n";
    return;
}

/** @noinspection PhpIncludeInspection */
require $file;



$rootPath = realpath(__DIR__ . "/../");


$composer = file_get_contents(__DIR__ . "/../composer.json");
$json = json_decode($composer, true);
$psr4 = $json["autoload"]["psr-4"];


$excluded = [];
if (isset($json["autoload"]["files"])) {
    $files = $json["autoload"]["files"];
    foreach ($files as $file) {
        $excluded["$rootPath/$file"] = true;
    }
}


// 检查自动加载
$iter = parsePsr4($psr4, $rootPath, $excluded);

$autoload = true;

foreach ($iter as $class) {
    if (interface_exists($class, $autoload) || class_exists($class, $autoload)) {
        echo "[\033[1;32mOK\033[0m] $class\n";
    } else {
        echo "[\033[1;31mKO\033[0m] $class\n";
    }
}


/**
 * 必须从细到粗定义, 比如先src/subDir, 再src
 * "Zan\\Framework\\Contract\\": "src/Contract",
 * "Zan\\Framework\\Foundation\\": "src/Foudation",
 * "ZanPHP\\Contracts\\": "src/"
 * @param array $psr4
 * @param $rootPath
 * @param array $excluded
 * @return Generator
 */
function parsePsr4(array $psr4, $rootPath, $excluded = [])
{
    natsort($psr4);
    $psr4 = array_reverse($psr4);

    foreach ($psr4 as $nsPrefix => $relativePath) {
        $dir = "$rootPath/$relativePath";
        $iter = dirIter($dir);
        foreach ($iter as $realPath) {
            if (isset($excluded[$realPath])) {
                continue;
            } else {
                $excluded[$realPath] = true;
            }

            $tmp = substr($realPath, strlen("$rootPath/$relativePath/"));
            if (strtolower(substr($tmp, -4)) !== ".php") {
                continue;
            }
            $path = substr($tmp, 0, -4);
            $ns = str_replace("/", "\\", $path);

            yield $nsPrefix . $ns;
        }
    }

}

function dirIter($dir, $regex = '/.*\.php$/')
{
    $iter = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
    $iter = new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::LEAVES_ONLY);
    $iter = new \RegexIterator($iter, $regex, \RegexIterator::GET_MATCH);

    foreach ($iter as $file) {
        $realPath = realpath($file[0]);
        yield $realPath;
    }
}