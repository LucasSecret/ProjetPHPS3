<?php
/**
 * Created by PhpStorm.
 * User: sntri
 * Date: 10/10/2017
 * Time: 18:52
 */

include('../classes/SQLServices.php');

initVariables();

// Check if image file is a actual image or fake image
checkThereIsImage();
// Check if file already exists
checkExistingFile();
// Check file size
checkImageSize();
// Allow certain file formats
checkImageFormat();

// If there are no errors, then upload image
uploadImage();

function initVariables()
{
    include('../includes/variables.inc.php');
    global $target_dir, $target_file, $imageFileType, $sqlService;

    $target_dir = "../images_copyright/";
    $target_file = $target_dir . basename($_FILES['pictureToUpload']["name"]);
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    
    $sqlService = new SQLServices($hostnameDB,$dbName, $userDB, $passwordDB);
}


function checkThereIsImage()
{
    if(isset($_POST["submit"]))
    {
        $check = getimagesize($_FILES['pictureToUpload']["tmp_name"]);
        if($check == false)
            header("Location:../admin/adminIndex.php?error=errorNoImage");
    }
}

function checkExistingFile()
{
    global $target_file;

    if (file_exists($target_file))
        header('Location:../admin/adminIndex.php?error=errorExistingFile');
}
function checkImageSize()
{
    if ($_FILES['pictureToUpload']["size"] > 500000)
        header('Location:../admin/adminIndex.php?error=errorSize');
}

function checkImageFormat()
{
    global $imageFileType;

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")
        header('Location:../admin/adminIndex.php?error=errorFormat');
}

function uploadImage()
{
    global $target_file, $sqlService, $imageFileType;

    if (!(move_uploaded_file($_FILES['pictureToUpload']["tmp_name"], $target_file)))
        header('Location:../admin/adminIndex.php?error=errorUpload');

    else
    {
        $sqlService->insertData('image', array(
            array(
                'name' => $_FILES['pictureToUpload']["name"],
                'extension' => $imageFileType,
                'price' => $_POST['price']
            )));

        add_copyright($_FILES['pictureToUpload']['name'], $imageFileType);
        header('Location:../admin/adminIndex.php?error=noError');
    }
}

/*
 * @param $filePath
 */
function add_copyright($fileName, $imageFileType)
{
    if($imageFileType == "png")
        $photo = imagecreatefrompng("../images_copyright/$fileName"); //A mettre en JPEG

    else
        $photo = imagecreatefromjpeg("../images_copyright/$fileName"); //A mettre en JPEG


    $width_photo = imagesx($photo);
    $height_photo = imagesy($photo);


    $couleur = imagecolorallocate($photo, 255,255,255); //Définition de la couleur
    imageline($photo,0,0,$width_photo,$height_photo,$couleur); //Création des lignes
    imageline($photo,$width_photo,0,0,$height_photo,$couleur);

    unlink ("../images_copyright/$fileName");

    imagejpeg($photo, "../images_copyright/$fileName"); //Enregistrement de la photo

    imagedestroy($photo);
}


?>

