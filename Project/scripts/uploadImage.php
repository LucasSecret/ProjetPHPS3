<?php

include('../classes/SQLServices.php');
include('../classes/ImageHandler.php');
include('../includes/variables.inc.php');

global $imageHandler, $sqlService;

$sqlService = new SQLServices($hostnameDB,$dbName, $userDB, $passwordDB);
$imageHandler = new ImageHandler($sqlService);


$target_dir = "C://Users/sntri/Documents/IUT/2eme_Annee/PHP/ProjetPHPS3/Project/images_copyright/";
$target_file = $target_dir . basename($_FILES['pictureToUpload']["name"]);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
if(!checkThereIsImage())
    header("Location:../admin/admin_panel.php?error=errorNoImage");

// Check if file already exists
elseif(!checkExistingFile())
    header("Location:../admin/admin_panel.php?error=errorExistingFile");

// Check file size
elseif(!checkImageSize())
    header('Location:../admin/admin_panel.php?error=errorSize');

// Allow certain file formats
elseif(!checkImageFormat())
    header('Location:../admin/admin_panel.php?error=errorFormat');

// If there are no errors, then upload image
else
    uploadImage();




function checkThereIsImage()
{
    if(isset($_POST["submit"]))
    {
        $check = getimagesize($_FILES['pictureToUpload']["tmp_name"]);
        if($check == false)
            return false;
        return true;
    }
}

function checkExistingFile()
{
    global $target_file;
    if (file_exists($target_file))
        return false;
    return true;
}

function checkImageSize()
{
    if ($_FILES['pictureToUpload']["size"] > 500000)
        return false;
    return true;
}

function checkImageFormat()
{
    global $imageFileType;
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")
        return false;
    return true;
}

function uploadImage()
{
    global $imageHandler, $target_file, $imageFileType;
    $imageHandler->uploadImageInDB($_FILES['pictureToUpload']["tmp_name"], $target_file, $imageFileType);
}






?>