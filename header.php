<?php
include_once "../../mainfile.php";
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include_once "common/functions.php";

if (file_exists("language/".$xoopsConfig['language']."/modinfo.php")) {
    include_once "language/".$xoopsConfig['language']."/modinfo.php";
} else {
    include_once "language/english/modinfo.php";
}

$myts =& MyTextSanitizer::getInstance();
?>