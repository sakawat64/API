<?php

require('model/Mikrotik.php');
$mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');

$mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);


date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$notification = "";
//taking month and years
$day = date('M-Y');

$allAgentData = $obj->view_selected_field_by_cond('tbl_agent', 'ip as ip', 'ag_id > 1400');
$i = 0;
//foreach($allAgentData as $agent){
//    echo $agent['ip'];
//    echo ' - '.$i++."<br>";
//    $mikrotik->createNewSecret(trim($agent['ip']), 1234, 'pppoe', array_rand(['256 Kbps' => 1, '512 Kbps' => 2],1));
//}

if ($mikrotik->checkConnection()) {

    $interface = $mikrotik->interfaceStatus();
    $profile = $mikrotik->profileStatus();
    ?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2 margin_15_px">
            <div class="alert alert-success text-center">
                <strong>Welcome To Mikrotik</strong>
                <br>
                <small>Successfully connected to Mikrotik Router by IP
                    :<?php echo $mikrotikLoginData['mik_ip']; ?></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2" style="height:320px;">
            <div class="preDataMikrotik" style="position:absolute; top:30%; left:20%;">
                <div class="col-md-12 text-center">
                    <h2 class="btn-block"><span
                                class="border-teal-800 glyphicon glyphicon-transfer text-teal-800"></span></h2>
                </div>
                <div class="col-md-12 text-center" style="margin-top:10px;">
                    <p class="text-muted">Please Wait</p>
                    <h4 class="text-teal-800">While we configure your LAN Speed ...</h4>
                </div>
            </div>
            <canvas id="microtikTraficGraph"></canvas>
        </div>
    </div>
    <hr>
    <div class="row">
        <h4 class="text-center"><b>Other Information</b></h4>
        <div class="col-md-6">

            <div class="table-responsive" style="padding-top:10px;">
                <p class="text-center  bg-info"><strong>Interface List</strong></p>
                <table class="table table-striped no-margin">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Running</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($interface as $singleInterface) { ?>
                        <tr>
                            <td><?php echo $singleInterface['name'] ?></td>
                            <td><?php echo $singleInterface['type'] ?></td>
                            <td>
                                <?php if ($singleInterface['running'] == 'false') {
                                    echo '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
                                } else {
                                    echo '<span class="label label-success"><span class="glyphicon glyphicon-random"></span></span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($singleInterface['disabled'] == 'false') {
                                    echo '<span class="label label-success">Enable</span>';
                                } else {
                                    echo '<span class="label label-danger">Disable</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive">
                <p class="text-center bg-info"><strong>Profile List</strong></p>
                <table class="table no-margin table-striped">
                    <tbody>
                    <?php foreach ($profile as $singleProfile) { ?>
                        <tr>
                            <td><?php echo $singleProfile['name'] ?></td>
                            <td><?php echo isset($singleProfile['rate-limit']) ? $singleProfile['rate-limit'] : "No Data" ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="asset/js/Chart.js"></script>
    <script type="text/javascript" src="mikrotik/mikrotik_assets/js/custom_chart_mikrotik.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').on('click', 'tbody tr td button#secretCangeStatus', function (e) {
                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();
                $.ajax({
                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, state: status},
                    success: function (result) {

                    }
                });
                if (status == 'Enable') {
                    $(this).html('Disable');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-danger');
                } else {
                    $(this).html('Enable');
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-success');
                }
            });
        });
    </script>
    <?php

} else {
    echo '<div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik</strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>';
}
?>


