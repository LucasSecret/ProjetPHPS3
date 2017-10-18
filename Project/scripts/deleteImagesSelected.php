<?php

include('../classes/SQLServices.php');
include('../classes/ImageHandler.php');
include('../includes/variables.inc.php');

$sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
$imageHandler = new ImageHandler($sqlService);
$imagesSelectedGET = $_GET['selectedImages'];

$listOfSelectedImage = preg_split('[,]', $imagesSelectedGET);

$imageHandler->deleteKeywordAssociatedToImage($listOfSelectedImage);
$imageHandler->deleteArrayOfImage($listOfSelectedImage);

header('Location:../admin/admin_panel.php');
?>