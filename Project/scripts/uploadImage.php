<?php

include('../classes/SQLServices.php');


initVariables();

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
        header("Location:../admin/admin_panel.php?error=errorUpload");
    else
    {
        $sqlService->insertData('image', array(
            array(
                'name_image' => $_FILES['pictureToUpload']["name"],
                'extension' => $imageFileType,
                'price' => $_POST['price']
            )));
        insertKeywordsInDB($sqlService);
        add_copyright($_FILES['pictureToUpload']['name'], $imageFileType);
        header("Location:../admin/admin_panel.php?error=noError");
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


function keywordsSelected($sqlService)
{
    $checkBoxesName = $sqlService->getData('keyword', 'id_keyword');
    $keywordsSelected = "";
    foreach ($checkBoxesName as $key => $line)
    {
        foreach ($line as $column => $id_checkBox)
        {
            if(isset($_POST["$id_checkBox"."_checkbox"]))
                $keywordsSelected .= ",$id_checkBox";
        }
    }
    $keywordsSelected = substr($keywordsSelected,1);
    $keywordsSelected = explode(',', $keywordsSelected);
    return $keywordsSelected;
}


function insertKeywordsInDB($sqlService)
{
    $imageID = $sqlService->getData('image', 'id_image', array("where" => "name_image = '".$_FILES["pictureToUpload"]["name"]."'"));
    $keywordArray = keywordsSelected($sqlService);
    $imageID = extractValueFromArray($imageID);
    foreach ($keywordArray as $key => $keyword)
    {
        $sqlService->insertData('image_keyword', array(
            array(
                'id_image' => $imageID,
                'id_keyword' => $keyword,
            )));
    }
}
function extractValueFromArray($array)
{
    foreach ($array as $key => $value)
    {
        foreach ($value as $key_value => $value_value)
            return $value_value;
    }
}
?>