<?php
session_start();

if (!empty($_SESSION['UserId'])) {


    //========================================
    include '../../model/oop.php';
    include '../../model/Bill.php';
    require('../../model/Mikrotik.php');

    $obj = new Controller();
    $bill = new Bill();

    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);

//========================================

    $mikrotik_connect = false;
    if ($mikrotik->checkConnection()) {

        $profile = $mikrotik->profileStatus();
        $mikrotik_connect = true;

    }

    $allMikrotikUser = $mikrotik->viewAllPppSecret();


    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');

    $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
    $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
    $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
    $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

    //taking month and years
    $day = date('M-Y');

    if (isset($_GET['zone'])) {

        $zone = $_GET['zone'];

        $allDuePayment = $obj->view_all_by_cond("vw_agent", "ag_status='1' AND pay_status='1' AND due_status='0' AND zone = $zone ORDER BY `vw_agent`.`ag_id` ASC");

    } else {

        $allDuePayment = $obj->view_all_by_cond("vw_agent", "ag_status='1' and pay_status='1' AND due_status='0' ORDER BY `vw_agent`.`ag_id` ASC");
    }

    $duePaymentArrForJson = array();

    $total_due_amount = 0;

    $i = -1;

    foreach ($allDuePayment as $parentKey => $value) {

        $i++;

        $all_due = $bill->get_customer_dues(isset($value['ag_id']) ? $value['ag_id'] : NULL);

        $previousDue = ($all_due - (isset($value['taka']) ? $value['taka'] : 0) );

        $total_due_amount += $all_due;

        $ip = trim($value['ip']);

        $duePaymentArrForJson[] = array(

            'agent_id' => $value['ag_id'],
            'agent_name' => $value['ag_name'],
            'customer_id' => $value['cus_id'],
            'agent_address' => $value['ag_office_address'],
            'zone' => $value['zone_name'],
            'mobile' => $value['ag_mobile_no'],
            'speed' => $value['mb'],
            'previous_due' => $previousDue,
            'bill' => (int)$value['taka'],
            'total_due' => $all_due,
            'ip' => $ip,
            'mikrotik_disconnect' => $value['mikrotik_disconnect'].'-'.date('m-Y'),
            'disabled' => null,

        );

        foreach ($allMikrotikUser as $childKey => $mikrotik) {

            $name = array_search($ip, $mikrotik);

            if (isset($mikrotik[$name])) {

                $duePaymentArrForJson[$i]['disabled'] = $mikrotik['disabled'];

                unset($allMikrotikUser[$childKey]);

                continue 2;
            }
        }


    }// foreach loop ends here

    echo json_encode((array('data' => $duePaymentArrForJson, 'total_bill' => $total_due_amount)));

} else {
    header("location: include/login.php");
}
?>