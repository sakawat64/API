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

$rowCount = $obj->Total_Count("vw_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

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

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>

        .ui-progressbar {
            position: relative;
        }

        .progress-label {
            position: absolute;
            left: 50%;
            top: 4px;
            font-weight: bold;
        }

        .ui-widget-header {
            border: 1px solid #5467f7;
            background: #7b8af7;
            color: #333333;
            font-weight: bold;
        }

        .outer {
            display: table;
            position: absolute;
            height: 350px;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: bottom;
        }

    </style>
    <div class="row ">
        <div class="outer">
            <div class="middle">
                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-info">

                        <div class="panel-heading">
                            <div id="introText" class="text-center">
                                <h3> Please have patience </h3>
                                <h4>After calculating your bill, We redirect to Mikrotik support</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="center-block">
                                <div class="progress " id="progressbar">
                                    <div class="progress-label">Calculating...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function () {

            var progressbar = $("#progressbar"),
                progressLabel = $(".progress-label");

            progressbar.progressbar({
                value: false,
                change: function () {
                    progressLabel.text(progressbar.progressbar("value") + "%");
                },
                complete: function () {
                    progressLabel.text("Redirecting.....");
                }
            });

            function progress() {
                var val = progressbar.progressbar("value") || 0;

                progressbar.progressbar("value", val + 2);

                setTimeout(progress, <?php echo $rowCount * 3; ?>);
            }

            setTimeout(progress, 300);

        });

        $.get("view/ajax_action/ajax_check_bill.php", function (data) {

            if (data.status == 1) {
                window.location = "?q=mikrotik_all_dues";
            } else {

                $('.row .outer .middle .panel #introText').html('<h4> Sorry! An Error Occure</h4>');
                $('.row .outer .middle .panel').removeClass('panel-info');
                $('.row .outer .middle .panel').addClass('panel-danger');
            }
        }, "json");



    </script>

    <?php

} else {
    echo '<div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik</strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>';
}
?>
