<?php
include ('../../../include/cp_header.php');

xoops_cp_header();
include_once "functions.php";

include_once "../include/functions.php";

if (file_exists("../language/".$xoopsConfig['language']."/modinfo.php")) {
    include_once "../language/".$xoopsConfig['language']."/modinfo.php";
} else {
    include_once "../language/english/modinfo.php";
}

if (file_exists("../language/".$xoopsConfig['language']."/admin.php")) {
    include_once "../language/".$xoopsConfig['language']."/admin.php";
} else {
    include_once "../language/english/admin.php";
}

if (file_exists("../language/".$xoopsConfig['language']."/main.php")) {
    include_once "../language/".$xoopsConfig['language']."/main.php";
} else {
    include_once "../language/english/main.php";
}


$myts = myTextSanitizer::getInstance();
?>