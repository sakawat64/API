<?php
require('model/Mikrotik.php');


$mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
$mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);

if (!$mikrotik->checkConnection()) {

    echo '<div class="col-md-12" style="margin-top:20px;">
    <div class="text-center">
        <div class="alert alert-danger">
            Sorry ! System cant connect with  Mikrotik <a href="?q=mikrotik_configure" class="btn btn-xs btn-danger">Click Here</a>  to configure.
        </div>
        
    </div>';

    die();

}

$profile = $mikrotik->profileStatus();
$allMikrotikUser = $mikrotik->viewAllPppSecret();

$mikrotik_rule_data = $obj->details_by_cond('mikrotik_rule', 'role = "disconnect"');

if (empty($mikrotik_rule_data)) {
    $form_data_create_rule = array(
        'role' => 'disconnect',
        'day' => '0',
        'action' => '2017-1-1',
        'implement' => '0',
        'created_by' => '0',
    );
    $obj->Reg_user_cond('mikrotik_rule', $form_data_create_rule, '');

    $mikrotik_rule_data = $obj->details_by_cond('mikrotik_rule', 'role = "disconnect"');
}

// ************************ Mikrotik rule add ********************

if (isset($_POST['discDateChange'])) { // change all customer disconnect date

    $preDateArray = explode('-',$_POST['pre_date']);
    $preDate = $preDateArray[0];

    $changedDateArray = explode('-',$_POST['changed_date']);
    $changedDate = $changedDateArray[0];
    $obj->Update_data('tbl_agent', ['mikrotik_disconnect'=>$changedDate], 'mikrotik_disconnect = '.$preDate);

}

if ($mikrotik_rule_data['action'] != date('Y-m-d')) { // need this value in javascript / ajax  part

    $disconnectCount = $obj->Total_Count("vw_agent", "ag_status='1' and pay_status='1' AND due_status='0' AND mikrotik_disconnect > " . date('d', strtotime($mikrotik_rule_data['action'])) . " AND mikrotik_disconnect <= " . date('d') . "");
}

$rowCount = $obj->Total_Count("vw_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->


<script src="asset/js/jquery-ui.js"></script>
<style>

    .ui-progressbar {
        position: relative;
    }

    .progress-label {
        text-align: center;
        position: absolute;
        left: 50%;
        font-weight: bold;
        /*text-shadow: 1px 1px 0 #fff;*/
    }

    .ui-widget-header {
        border: 1px solid #2f7d2f;
        background: #5cb85c;
        color: #333333;
        font-weight: bold;
    }

    #datatable_mikrotk_view_due_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12 bg-grey-800"
         style="margin-top:10px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; font-weight:bold;">
        <div class="row">
            <div class="col-md-4">
                <b>View Customer Billing Information <?php echo date('M'); ?></b>
            </div>
            <?php if ($ty == 'SA') { ?>
                <div class="col-md-4" style="font-family: Helvetica;">
                    <div class="text-center">
                        <p>Total Bill Amount : <b><span id="billAmount"> <span class="glyphicon
                                                                               glyphicon-transfer"></span> .....</span></b>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="float:right; padding-right:10px">
                        <a id="print_client_bill" class="btn btn-primary btn-sm"
                           href="?q=print_client_bill">Print
                            Client Bill <span class="glyphicon glyphicon-print"></span></a>
                        <a class="btn btn-primary btn-sm" href="?q=add_agent">Add New <span
                                    class="glyphicon glyphicon-plus"></span></a>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<div class="row" style="margin-bottom:10px;">
    <div class="col-md-4">
        <div class="btn-group" role="group">
            <a class="btn btn-primary btn-sm" id="print_link" href="?q=view_report_paganition&flag=INVOICE"
               target="_blank">Print
                Invoice</a>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#changeAllDisconnectDate">Change All Disconnect Date</button>
        </div>
    </div>

    <div class="col-md-4 col-md-offset-4">
        <?php if ($ty == 'SA' || $ty == 'A') {
            ?>
            <select class="form-control" name="zone" required>
                <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                    <option></option>
                    <option value="<?php echo $singleZone['zone_id'] ?>">
                        <?php echo $singleZone['zone_name'] ?> - (<?php
                        echo $obj->Total_Count('tbl_agent', "zone = " . $singleZone['zone_id']
                            . "");
                        ?>)
                    </option>
                <?php }// foreach   ?>
                <option value="x">Reset</option>
            </select>
        <?php }
        ?>
    </div>
</div>

<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg != null) {
    ?>
    <div class="col-md-12">
        <div class="alert alert-info">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Message: </strong><?= $msg ?>
        </div>
    </div>
    <?php
}
?>
<div class="row" style="font-size:12px">
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover table-striped" id="datatable_mikrotk_view_due">
            <thead>
            <tr>
                <th class="col-md-1">Customer</th>
                <th class="col-md-1">IP</th>
                <th class="col-md-1">Address</th>
                <th class="col-md-1">Zone</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">Pre. Due</th>
                <th class="col-md-1">Bill</th>
                <th class="col-md-1">Total<br>Dues</th>
                <th class="col-md-2">Disc.<br>Date</th>
                <th class="d col-md-1">Status</th>
                <th class="d col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <div class="progress" id="progressbar">
            <div class="progress-label">Calculating Your Bill Data...</div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade " id="disconnectSecretProcessing">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Disconnecting The Due Customer <span class="label label-default" id="offset"></span> .... </h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="background: #F9F9F9">
                        <h4 class="text-center"><i>Please Do not Close This Page Until Processing is finished</i></h4>

                        <div class="col-md-12 text-center">
                            <img style="width: 113px;height: auto;" src="asset/img/processing.gif" alt="">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal of Change disconnect date of single customer -->
<div class="modal" id="datechange" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Disconnect Date</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="datechangeform" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                    <div class="form-group">
                        <input class="datepick form-control" name="date" id="modal_date" type="text" value="">
                    </div>

                    <input type="hidden" id="change_date_agent_id" name="id" value="">

                    <div class="form-group">
                        <input type="submit" class="btn btn-sm btn-primary" value="Save">
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>







<!-- Modal of change all customer disconnect date -->
<div id="changeAllDisconnectDate" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Disconnect Date update for All Customer</h4>
            </div>
            <form method="post" action="" class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="pre_date">Date you want to update</label>
                        <div class="form-group">
                            <input class="datepick form-control" name="pre_date" type="text" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="pre_date">Insert Changed Date</label>
                        <div class="form-group">
                            <input class="datepick form-control" name="changed_date" type="text" value="">
                        </div>
                    </div>
                    <input type="hidden" id="change_date_agent_id" name="id" value="">

                    <div class="col-md-12 text-center">
                        <div class="form-group">
                            <input type="submit" name="discDateChange" class="btn btn-sm btn-primary" value="Changed the date">
                        </div>
                    </div>
                </div>
            </form>

        </div>

    </div>
</div>
<script>

    function confirmAction() {

        return confirm("Are You Sure Change State of this secret? ");
    }
    $(document).ready(function () {

        var progressbar = $("#progressbar");
        var progressLabel = $(".progress-label");

        /*  ===========================================================
        *   to show a progress bar
        *   ===========================================================*/
        progressbar.progressbar({
            value: false,
            change: function () {
                progressLabel.text(progressbar.progressbar("value") + "%");
            },
            complete: function () {

            }
        });

        function progress() {
            var val = progressbar.progressbar("value") || 0;
            progressbar.progressbar("value", val + 2);

            if (val < 99) {

                setTimeout(progress, <?php echo $rowCount * 0.1; ?>);
            }
        }

        setTimeout(progress, 500);

        /*  ===========================================================
         *  pay a customer when user click paid button
         *   ==========================================================*/

        $('#datatable_mikrotk_view_due').on('click', 'tbody td a.paid', function (e) {

            e.preventDefault();
            $(this).removeClass('btn-info');
            $(this).addClass('disabled btn-default');
            var agentName = $(this).data('name');
            var amountTaka = $(this).data('amount');

            url = $(this).attr('href');

            $.get(url, function (data, status) {

                if (status == 'success') {

//                    alert('The Bill of ' + amountTaka + 'tk has been paid for ' + agentName);

                } else {

                    alert('Sorry cant add the bill info. Error occurred');
                }
            });
        });

        /*  ===========================================================
         *  show bill amount data in top by ajax
         *   ==========================================================*/

        $.post("view/ajax_action/ajax_data_return.php", function (data) {

            $('#billAmount').html(data.duePayment.toLocaleString() + " Taka");

        }, "json");

        /*  ===========================================================
         *   initialize data table and show data
         *   ===========================================================*/

        table = $('#datatable_mikrotk_view_due').DataTable({
            "processing": false,
            "initComplete": function (settings, json) {
                $('#progressbar').hide();
            },
            "deferRender": true,
            "ajax": 'mikrotik/mikrotik_assets/ajax_view_due_payment_microtik.php',
            "order": [[0, 'asc']],
            "columns": [

                {"data": 'agent_name'},

                {"data": 'ip'},

                {"data": 'agent_address'},

                {"data": 'zone'},

                {"data": 'mobile'},

                {"data": 'speed'},

                {"data": 'previous_due'},

                {"data": 'bill'},

                {
                    "data": 'total_due',
                    render: function (data, type, row) {
                        if (row['previous_due'] > 0) {
                            return '<span class="text-danger" style="font-weight:800; font-size:12px">' + data + '</span>'
                        } else {
                            return '<span>' + data + '</span>'
                        }

                    }
                },

                {
                    "data": 'mikrotik_disconnect',
                    "render": function (data, type, row, meta) {

                        return '<span class="disconnect_date" data-id="'+ row['agent_id'] +'" ><span class="disconnectd">' + data + '</span>&nbsp;<span  class="pointer text-primary glyphicon glyphicon-pencil"></span></span>';
                    }
                },

                {
                    "data": 'disabled',
                    "render": function (data, type, row, meta) {

                        if (data) {

                            var micStatus;

                            if (data == 'true') {

                                return '<button id="secretChangeStatus" data-name="' + row['ip'] + '" class="btn btn-xs btn-danger">Disable</button>';
                            } else if (data == 'false') {

                                return '<button id="secretChangeStatus" data-name="' + row['ip'] + '" class="btn btn-xs btn-success">Enable</button>';
                            } else {
                                return  null;
                            }
                        }
                    }
                },

                {
                    "data": 'agent_id',
                    "render": function (data, type, row, meta) {
                        return '<a href="view/ajax_action/add_ajax_data.php?token=' + data + '&amount=' + row['bill'] + '&flag=1" data-name="' + row['agent_name'] + '" data-amount="' + row['bill'] + '" class="btn btn-xs btn-primary paid" >Paid </a>' +
                            '<a href="?q=add_payment&token1=' + data + '"  class="btn btn-xs btn-info"> Edit </a>';                    }
                },

            ],

        });


        /*  ===========================================================
         *  when zone select show this zone data
         *  ===========================================================*/

        $('select[name="zone"]').on('change', function () {

            var zoneId = $(this).val();

            if (zoneId != 'x') {

                table.ajax.url('mikrotik/mikrotik_assets/ajax_view_due_payment_microtik.php?zone=' + zoneId).load();
                $('a#print_link').attr('href', './pdf/index.php?zonePrint=' + zoneId);
                $('a#print_client_bill').attr('href', '?q=print_client_bill&zonePrint=' + zoneId);

            } else {

                table.ajax.url('mikrotik/mikrotik_assets/ajax_view_due_payment_microtik.php').load();
                $('a#print_link').attr('href', '?q=view_report_paganition&flag=INVOICE');
                $('a#print_client_bill').attr('href', '?q=print_client_bill');

            }
        });



        /*  ===========================================================
         *  show modal of change single customer disconnect date
         *  ===========================================================*/

        $('#datatable_mikrotk_view_due').on('click', 'tbody tr td span.disconnect_date', function (e) {

            var txt = $(this).find('span.disconnectd').html();
            var id = $(this).data('id');


            $("#datechange #modal_date").val(txt);
            $("#datechange #change_date_agent_id").val(id);
            $("#datechange").modal("show");

        }); // when single customer disconnect date change

        /*  ===========================================================
         *  when form submitted of single customer disconnect date
         *  ===========================================================*/

        $("form#datechangeform").submit(function(e){

            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "mikrotik/change_disable_date.php",
                data: $("form#datechangeform").serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $("#datechange").modal("hide");
                    table.ajax.reload();
                }
            });

        }); // form submit of single customer disconnect date change


        /*  ===========================================================
         *   secret status change enable or disable
         *  ===========================================================*/

        $('#datatable_mikrotk_view_due').on('click', 'tbody tr td button#secretChangeStatus', function (e) {

//            if( confirmAction() ){

                var thisSecret = $(this);

                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();

                $.ajax({

                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, state: status},

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

//            }
        }); // secret enable disable


        $('[data-toggle="tooltip"]').tooltip();

        <?php

        /*  ===========================================================
         *  if today disconnect action is not done then initialize and add it to database
         *  ==========================================================*/

        if ($mikrotik_rule_data['action'] != date('Y-m-d')) {

            echo 'disableSecret( ' . intval(floor($disconnectCount / 50)) . ' );';
            echo ' $("#disconnectSecretProcessing").modal("show");';
        }

        ?>

    });

    /*  ===========================================================
     *  function to disable secret
     *   ==========================================================*/

    function disableSecret(number) {

        if (number < 0) {

            $.get("mikrotik/disableAllSecret.php", {action: 'db_status'}, function (data) {

                table.ajax.reload();
                $("#disconnectSecretProcessing").modal("hide");
            });

            return;
        }

        $.get("mikrotik/disableAllSecret.php", {action: 'secret_off', offset: (number * 50)}, function (data) {

            $("#disconnectSecretProcessing .modal-dialog .modal-content .modal-header #offset").html(data);
        });

        disableSecret(number - 1);

    }

    /*  ===========================================================
     *
     *   ==========================================================*/

    $('.datepick').datepicker({

        dateFormat: 'dd-mm-yy',
    });
</script>


