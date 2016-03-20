<?php
session_start();

if (empty($_SESSION)) {
    header('location: index.php');
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>SN!</title>
        <link rel="stylesheet" href="scripts/style.css" type="text/css" />
        <link rel="stylesheet" href="scripts/jquery/ui/jquery-ui.css">
        <link rel="stylesheet" href="scripts/imgareaselect/imgareaselect.css">
    </head>
<body>
<?php include('headers/header.php'); ?>
    <div id="content">
        <div class="wrapper">
            <div id="updated" style="display: none;">
                <center><h1>Successfully updated!</h1></center>
            </div>
            
            <div class="panel left" id="editacct">
                <center>
                <div class="ui-widget">
                    <div class="ui-state-error ui-corner-all" style="width: 60%; display: none;"  align="left" id="errorContainer">
                        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .8em; margin-top: 0;"></span><strong>Error!</strong><ul id="errorList"></ul></p>
                    </div>
                </div>

                <div id="dialog" title="Select an area to crop">
                    <div id="preview-avatar"></div>
                </div>

                <h1>Edit account information</h1>
                <form method="POST" id="updateForm">
                    <input name="userid"    type="hidden"   value="<?= $_SESSION['id'];?>" id="userid">
                    <input name="username"  type="text"     value="<?= $_SESSION['username'];?>" id="username" disabled>
                    <input name="email"     type="text"     placeholder="New Email" id="email" value="<?php if (isset($_POST['email'])) {echo $_POST['email'];}?>">
                    <input name="newpassword"   type="password" placeholder="New Password" id="newpassword">
                    <input name="newpassword2" type="password" placeholder="Confirm New Password" id="newpassword2">
                    <br />
                    <p>
                    To finalize your changes please type in your current password.<br />
                    <input name="currentpass" type="password" placeholder="Current Password" id="currentpass">
                    </p>
                    <input type="submit"    value="Update Account">
                </form> 
                </center>
            </div>

            <div class="panel right" id="editacct_photo">
            <center>
                <h1>Upload a new profile picture</h1>
                <p align="left">
                Current profile picture:<br />
                <img src="<?=$_SESSION['avatar'];?>" id="currentavatar">
                </p>
                <form id="photoForm" method="POST" enctype="multipart/form-data" action="upload.php">
                    <input type="file"   name="image"            id="image">
                    <input type="hidden" name="hdn-profile-id"   id="hdn-profile-id"   value="<?=$_SESSION['id'];?>">
                    <input type="hidden" name="hdn-x1-axis"      id="hdn-x1-axis"      value="">
                    <input type="hidden" name="hdn-y1-axis"      id="hdn-y1-axis"      value="">
                    <input type="hidden" name="hdn-x2-axis"      id="hdn-x2-axis"      value="">
                    <input type="hidden" name="hdn-y2-axis"      id="hdn-y2-axis"      value="">
                    <input type="hidden" name="hdn-thumb-width"  id="hdn-thumb-width"  value="">
                    <input type="hidden" name="hdn-thumb-height" id="hdn-thumb-height" value="">
                    <input type="hidden" name="action"           id="action"           value="">
                    <input type="hidden" name="image_name"       id="image_name"       value="">
                </form>
            </center>
            </div>

        </div>
    </div>        
    <?php include_once('headers/footer.php'); ?>
    <script src="scripts/jquery/jquery.js"></script>
    <script src="scripts/jquery/validate.js"></script>
    <script src="scripts/jquery/form.js"></script>
    <script src="scripts/jquery/ui/jquery-ui.js"></script>
    <script src="scripts/imgareaselect/imgareaselect.js"></script>
    <script src="scripts/js/validation.js"></script>
    <script src="scripts/js/picupdate.js"></script>
</body>
</html>