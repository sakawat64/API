<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$mikrotikData = $obj->details_by_cond('mikrotik_user', 'id = 1');
if (isset($_POST['addMikrotikAdmin'])) {
    echo "<pre>"; print_r($_POST); echo "</pre>";

    $mikrotikNumber = count($_POST['mikrotik_name']);
    echo "<h2>$mikrotikNumber</h2>";


//    $form_data_mikrotik = array(
//
//        'mik_username' => $mikrotik_name,
//        'mik_password' => $mikrotik_password,
//        'mik_ip' => $mikrotik_ip,
//        'entry_by' => $userid,
//        'entry_date' => $date,
//        'update_by' => $userid,
//
//    );

//    $mikrotik_table_count = $obj->Total_Count('mikrotik_user', 'id != 0');
//    if ($mikrotik_table_count == 0) {
//        $mikrotik_admin_add = $obj->Reg_user_cond("mikrotik_user", $form_data_mikrotik, "");
//        $notification = "Admin User added Successfully";
//    } else {
//        $mikrotik_admin_update = $obj->Update_data("mikrotik_user", $form_data_mikrotik, "id = 1");
//        $notification = "Admin User added Successfully";
//    }
//
//    if (isset($mikrotik_admin_add) || isset($mikrotik_admin_update)) {
//        ?>
<!--        <script>window.location = "?q=mikrotik_configure";</script>-->
<!--        --><?php
//    }
}
?>
<!--===================end Function===================-->

<div class="col-md-12">

    <?php if (isset($notification)) { ?>
        <div class="alert alert-warning alert-dismissable fade in text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><?php echo $notification; ?></strong>
        </div>
    <?php } ?>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <h2 class="text-center text-slate">Welcome to Mikrotik Panel</h2>
    <p class="text-center text-slate-700"><strong>Please Enter Your Mikrotik Login Information</strong></p>

    <div class="col-md-3">
        <div class="alert alert-warning">
            <strong>Enable API port </strong> <br> IP -> Services -> Enable api port from WinBox .
        </div>
    </div>
    <div class="col-md-6">

        <form role="form" id="mikrotikRuleAdd" class="form-horizontal" method="post">
            <div id="mikrotikStart" class="row">
                <div id="mikrotikNumber0" class="col-md-12">
                    <hr>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Mikrotik Login Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="mikrotik_name[]" class="form-control"
                                   value="<?php echo isset($mikrotikData['mik_username']) ? $mikrotikData['mik_username'] : null; ?>"
                                   placeholder="Provide Mikrotik Admin Name" required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Mikrotik Password</label>
                        <div class="col-sm-8">
                            <input type="password" name="mikrotik_password[]" class="form-control" id="ResponsiveTitle"
                                   value="<?php echo isset($mikrotikData['mik_password']) ? $mikrotikData['mik_password'] : null; ?>"
                                   placeholder="Provide Login Password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Mikrotik Real IP</label>
                        <div class="col-sm-8">
                            <input type="text" name="mikrotik_ip[]" class="form-control" id="clientId"
                                   placeholder="Provide Connection IP"
                                   value="<?php echo isset($mikrotikData['mik_ip']) ? $mikrotikData['mik_ip'] : null; ?>"
                                   required="required">
                        </div>
                    </div>

                </div>

                <div id="mikrotikNumber1" class="col-md-12"></div>

            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary" name="addMikrotikAdmin">Save Mikrotik Admin</button>
                    <button id="addMikrotikAdminField" class="btn btn-info">Add Another Mikrotik </button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-3">
        <div class="alert alert-warning">
            <strong>Please!</strong> Check Your MIKROTIK API port is enable.
        </div>
    </div>
</div>

<script>

    var mikrotikNumber = 1;

    $('button#addMikrotikAdminField').on('click', function (e) {

        e.preventDefault();

        $('form#mikrotikRuleAdd .row div#mikrotikNumber' + mikrotikNumber).html(
            '<hr><div class="form-group"><label class="col-sm-4 control-label">Mikrotik Login Name</label><div class="col-sm-8"><input type="text" name="mikrotik_name[]" class="form-control" placeholder="Provide Mikrotik Admin Name" required="required"></div></div>' +
            '' +
            '<div class="form-group"><label class="col-sm-4 control-label">Mikrotik Password</label><div class="col-sm-8"><input type="password" name="mikrotik_password[]" class="form-control" id="ResponsiveTitle" placeholder="Provide Login Password"> </div></div>' +
            '' +
            '<div class="form-group"><label class="col-sm-4 control-label">Mikrotik Real IP</label><div class="col-sm-8"><input type="text" name="mikrotik_ip[]" class="form-control" id="clientId"placeholder="Provide Connection IP" required="required"></div></div>' +
            '');

        $('form#mikrotikRuleAdd div#mikrotikStart').append('<div id="mikrotikNumber'+ (mikrotikNumber+1) +'" class="col-md-12">');

        mikrotikNumber++;
    });

</script>
