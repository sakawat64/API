<?php

session_start();

if (!empty($_SESSION['UserId'])) {


    $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
    $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
    $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
    $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


//========================================
    include '../model/oop.php';
    require('../model/Mikrotik.php');
    $obj = new Controller();
    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');

    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);


    $mikrotik_rule_data = $obj->details_by_cond('mikrotik_rule', 'role = "disconnect"');

    $lastCheck = date('d', strtotime($mikrotik_rule_data['action']));

    if ($_GET['action'] == 'secret_off') {

        $offset = $_GET['offset'];

        $dueAgentData = $obj->view_all_by_cond("vw_agent", "ag_status='1' AND pay_status='1' AND due_status='0' AND mikrotik_disconnect > $lastCheck AND mikrotik_disconnect <= " . date('d') . " ORDER BY `vw_agent`.`ag_id` ASC LIMIT 50 OFFSET $offset");

        foreach ($dueAgentData as $dueAction) {

            $secretName = trim($dueAction['ip']);
            $mikrotik->disableSingleSecret($secretName);

        }
        echo $offset;

    } else if ($_GET['action'] == 'db_status') {

        $form_data_mikrotik_rule = array(
            'action' => date('Y-m-d'), // Action performed this day
            'implement' => 1, // Complete for this month
        );
        $obj->Update_data("mikrotik_rule", $form_data_mikrotik_rule, 'role = "disconnect"');
    }


}