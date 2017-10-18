<?php
/**
 * Created by PhpStorm.
 * User: sntri
 * Date: 09/10/2017
 * Time: 10:11
 */
include('../classes/SQLServices.php');
include('../classes/ImageHandler.php');
include('../includes/variables.inc.php');

session_start();
if($_SESSION['connected'] != 1)
{
    session_destroy();
    header('Location:../login.html');
}

$sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
$imageHandler = new ImageHandler($sqlService);
?>

<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="../js/selectImageOnClick.js"></script>
<!--    <script>alert('Ah');</script>-->
</head>
<body>

<form method="post" action="../scripts/uploadImage.php" style="width: 200px;" enctype="multipart/form-data">
    <label>Upload Photo</label>
    <input type="file" name="pictureToUpload" >
    <label for="price">Price</label>
    <input type="text" name="price" >

    <?php
    $imageHandler->displayCheckbox();
    ?>
    <input type="submit" name="submit" value="Post Picture" >
</form>

<div style="margin-top: 20px">
    <?php
    $imageHandler->displayImageWithKeyword(null, "admin");
    ?>

    <div>
        <input type="submit" id="deletePicture" value="Delete">
    </div>
</div>


</body>
</html>