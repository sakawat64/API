<?php
session_start();

if (!empty($_SESSION['UserId'])) {


    //========================================
    include '../model/oop.php';

    $obj = new Controller();

    $dateArray = explode('-',$_POST['date']);

    $day = $dateArray[0];

    $form_data = array(
        'mikrotik_disconnect' => $day,
    );

    $obj->Update_data("tbl_agent", $form_data,'ag_id = '.$_POST['id']);


}