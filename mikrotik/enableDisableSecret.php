<?php

session_start();

$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


if (!empty($_SESSION['UserId'])) {

//========================================
    include '../model/oop.php';
    require('../model/Mikrotik.php');
    $obj = new Controller();

    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);



    $secretName = $_GET['name'];
    $enableDisableState = $_GET['state']; // status : 1 = enable & 0 = disable



    if ($enableDisableState == 'Enable') {

        $mikrotik->disableSingleSecret($secretName);
        echo '0';

    } else if ($enableDisableState == 'Disable'){

        $mikrotik->enableSingleSecret($secretName);
        echo '1';

    }
}