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
if(!checkThereIsImage())
    header("Location:../admin/adminIndex.php?error=errorNoImage");

// Check if file already exists
elseif(!checkExistingFile())
    header("Location:../admin/adminIndex.php?error=errorExistingFile");

// Check file size
elseif(!checkImageSize())
    header('Location:../admin/adminIndex.php?error=errorSize');

// Allow certain file formats
elseif(!checkImageFormat())
    header('Location:../admin/adminIndex.php?error=errorFormat');

// If there are no errors, then upload image
else
    uploadImage();

function initVariables()
{
    include('../includes/variables.inc.php');
    global $target_dir, $target_file, $imageFileType, $sqlService;

    $target_dir = "C://Users/sntri/Documents/IUT/2eme_Annee/PHP/ProjetPHPS3/Project/images_copyright/";
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
    global $target_file, $sqlService, $imageFileType;

    if (!(move_uploaded_file($_FILES['pictureToUpload']["tmp_name"], $target_file)))
        header("Location:../admin/adminIndex.php?error=errorUpload");

    else
    {
        $sqlService->insertData('image', array(
            array(
                  'name' => $_FILES['pictureToUpload']["name"],
                  'extension' => $imageFileType,
                  'price' => $_POST['price']
                  )));

        add_copyright($_FILES['pictureToUpload']['name'], $imageFileType);
        header("Location:../admin/adminIndex.php?error=noError?$target_file");
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

    if($imageFileType == "png")
        imagepng($photo, "../images_copyright/$fileName"); //Enregistrement de la photo
    else
        imagejpeg($photo, "../images_copyright/$fileName"); //Enregistrement de la photo

    imagedestroy($photo);
}


?>