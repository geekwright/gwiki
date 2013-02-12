<?php
include_once "../../../mainfile.php";
include_once XOOPS_ROOT_PATH."/kernel/module.php";
include_once XOOPS_ROOT_PATH."/include/cp_functions.php";
include_once "../common/functions.php";

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

if ($xoopsUser) {
    $xoopsModule = XoopsModule::getByDirname(_MI_WIKIMOD_DIRNAME);
    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL."/", 3, _NOPERM);
        exit();
    }
} else {
    redirect_header(XOOPS_URL."/", 3, _NOPERM);
    exit();
}

$config_handler = &xoops_gethandler('config');
$xoopsModuleConfig = &$config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

$myts =& myTextSanitizer::getInstance();
?>