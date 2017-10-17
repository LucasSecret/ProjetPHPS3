<?php

include('../classes/SQLServices.php');
include('../includes/variables.inc.php');

$sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
$imagesSelectedGET = $_GET['selectedImages'];

$listOfSelectedImage = preg_split('[,]', $imagesSelectedGET);

foreach ($listOfSelectedImage as $key => $imageSelected)
{
    $idStringLength = stripos($imageSelected, '._image'); //Find the 'id' string position in the image name
    $imageSelected = substr($imageSelected, 0,$idStringLength); //Delete the 'id' attribute string from the image name
    $sqlService->removeData('image',"name_image = '$imageSelected'", 1);
    echo $imageSelected;
    unlink ("../images_copyright/$imageSelected");
}

header('Location:../admin/admin_panel.php');
?>