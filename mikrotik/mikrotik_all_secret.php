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

if ($mikrotik->checkConnection()) {
    $allSecretData = $mikrotik->viewAllPppSecret();
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
        <div class="col-md-12 mikrotikTable table-responsive">
           <div class="panel panel-default">
               <h4 class="text-center">All Microtik Secret Information</h4>
           </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Pasword</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">Service</th>
                    <th class="text-center">Local Add</th>
                    <th class="text-center">Remote Add</th>
                    <th class="text-center">Last Log out</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;
                foreach ($allSecretData as $secretData) {
                    ?>
                    <tr>
                        <td><?php echo $serial; ?></td>
                        <td><?php echo $secretData['name'] ?></td>
                        <td><?php echo $secretData['password'] ?></td>
                        <td><?php echo $secretData['profile'] ?></td>
                        <td><?php echo $secretData['service'] ?></td>
                        <td class="text-center"><?php echo  isset ($secretData['local-address']) ? ($secretData['local-address']) : '-' ?></td>
                        <td class="text-center"><?php echo  isset ($secretData['remote-address']) ? ($secretData['remote-address']) : '-' ?></td>
                        <td class="text-center">
                            <?php
                            if(isset($secretData['last-logged-out'])){

                                if(($secretData['last-logged-out'] != 'jan/01/1970 00:00:00')){
                                    echo ucfirst($secretData['last-logged-out']);
                                }else{
                                    echo '-';
                                }
                            } ?>
                        </td>
                        <td>
                            <?php
                            if ($secretData['disabled'] == 'false') {
                                echo '<button id="secretCangeStatus" data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-success">Enable</button>';
                            } else {
                                echo'<button id="secretCangeStatus"  data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Disable</button>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php $serial++;  } ?>
                </tbody>
            </table>
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


