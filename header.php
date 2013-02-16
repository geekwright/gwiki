<?php
include_once '../../mainfile.php';
include_once "include/functions.php";

if (file_exists('language/'.$xoopsConfig['language'].'/modinfo.php')) {
    include_once 'language/'.$xoopsConfig['language'].'/modinfo.php';
} else {
    include_once 'language/english/modinfo.php';
}

$myts = MyTextSanitizer::getInstance();
?>