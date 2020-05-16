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



    if ($enableDisableState == 'Pay') {

        //$mikrotik->disableSingleSecret($secretName);
       // $agent = $obj->details_by_cond('tbl_agent',"ip='$secretName'");
        $mikrotikDetails = $mikrotik->showSingleSecret(trim($secretName));
        /*foreach($mikrotik->profileStatus() as $singlePackage)
          {
            if($singlePackage['name'] =='NO PAY')
              {
                $disable = $singlePackage['name'];
              }
        }*/
        $disable = 'NO PAY';
        $mik_profile = $mikrotikDetails['profile'];
        $mik_user_name = $mikrotikDetails['name'];
        $mik_remote_ip = $mikrotikDetails['remote-address'];
        $mik_user_id = 'Non Payment';
        $obj->Update_data('tbl_agent',['previous_pack'=>$mik_profile],"ip='$secretName'");
        $mikrotik->updateOnlyprofile(trim($secretName), trim($disable), trim($secretName));
        $mikrotik->CreateFirewall(trim($mik_user_id), trim($mik_remote_ip), trim($mik_user_name));
        echo '0';

    } else if ($enableDisableState == 'No Pay'){

       // $mikrotik->enableSingleSecret($secretName);
       $mikrotikDetails = $mikrotik->showSingleSecret(trim($secretName));
       $mik_user_id = $mikrotikDetails['name'];
        $agent = $obj->details_by_cond('tbl_agent',"ip='$secretName'");
        $previous_profile = trim($agent['previous_pack']);
        $mikrotik->updateOnlyprofile(trim($secretName), $previous_profile, trim($secretName));
        $test = $mikrotik->RemoveFirewall(trim($mik_user_id));
        echo '1';

    }
}