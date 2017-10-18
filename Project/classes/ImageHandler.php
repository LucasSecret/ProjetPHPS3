<?php

class ImageHandler
{
    private $sqlService;


    function __construct($sqlService)
    {
        $this->sqlService = $sqlService;
    }



    function displayImageWithKeyword($idKeywords = null, $displayOnWhichPage)
    {
        if(!empty($idKeywords))
        {
            $tableJoin = "image i JOIN image_keyword ik ON i.id_image = ik.id_image
                            JOIN keyword k ON ik.id_keyword = k.id_keyword";
            $idKeywords = substr($idKeywords, 1 ); //Delete the first ','
            $keywordsArray = explode(',', $idKeywords); //Delete the ',' between each id_keywords and stock them in array

            if(sizeof($keywordsArray) > 1) //If there are several keywords given in parameters
            {

                $cptKeywords = sizeof($keywordsArray);
                $whereClause = "";
                foreach ($keywordsArray as $key => $idKeyword)
                {
                    if($cptKeywords<1 || $cptKeywords == sizeof($keywordsArray))
                        $whereClause .= "ik.id_keyword = $idKeyword ";
                    else
                        $whereClause .= "OR ik.id_keyword = $idKeyword ";
                    $cptKeywords-- ;
                }
                $optionsArray = ["where" => $whereClause];
                $imagesName = $this->sqlService->getData($tableJoin, 'distinct name_image', $optionsArray);

                if(is_array($imagesName))
                {
                    foreach ($imagesName as $key => $line)
                    {
                        foreach ($line as $column => $imageName){

                            if($displayOnWhichPage == "index")
                                echo "<img src=\"images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";
                            else
                                echo "<img src=\"../images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";

                        }
                    }
                }
            }
            else //If there is only one keyword given in parameters
            {
                $optionsArray = ["where" => "ik.id_keyword = $idKeywords"];
                $imagesName = $this->sqlService->getData($tableJoin, 'name_image', $optionsArray);
                if(is_array($imagesName)) //If there are several images returned by the query
                {
                    foreach ($imagesName as $key => $line)
                    {
                        foreach ($line as $column => $imageName){
                            if($displayOnWhichPage == "index")
                                echo "<img src=\"images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";
                            else
                                echo "<img src=\"../images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";
                        }
                    }
                }
                else
                {
                    if($displayOnWhichPage == "index")
                        echo "<img src=\"images_copyright/$imagesName\" alt=\"$imagesName\" id=\"$imagesName._image\" >";
                    else
                        echo "<img src=\"../images_copyright/$imagesName\" alt=\"$imagesName\" id=\"$imagesName._image\" >";
                }
            }
        }

        else //If no keywords in parameters
        {
            $imageName = $this->sqlService->getData('image', 'name_image');
            if (!is_null($imageName)) {
                foreach ($imageName as $key => $line)
                {
                    foreach ($line as $column => $value_column)
                        if($displayOnWhichPage == "index")
                            echo "<img src=\"images_copyright/$value_column\" alt=\"$value_column\" id=\"$value_column._image\" >";
                        else
                            echo "<img src=\"../images_copyright/$value_column\" alt=\"$value_column\" id=\"$value_column._image\" >";                }
            }
        }
    }


    function displayCheckbox()
    {
        $checkBoxesName = $this->sqlService->getData('keyword', 'id_keyword, name_keyword');
        if(!is_null($checkBoxesName))
        {
            foreach ($checkBoxesName as $key => $line)
            {
                foreach ($line as $column => $value_column)
                {
                    if($column == 'id_keyword')
                        $id = $value_column;
                    else
                        $keyword = $value_column;
                }
                echo "<label class=\"text-white\">$keyword<input type=\"checkbox\" name=" . "\"$id" . "_checkbox\"></label> ";
            }
        }
    }

    function deleteArrayOfImage($listOfSelectedImage)
    {
        foreach ($listOfSelectedImage as $key => $imageSelected)
        {
            $idStringLength = stripos($imageSelected, '._image'); //Find the 'id' string position in the image name
            $imageSelected = substr($imageSelected, 0,$idStringLength); //Delete the 'id' attribute string from the image name

            $this->sqlService->removeData('image',"name_image = '$imageSelected'", 1);

            unlink ("../images_copyright/$imageSelected");
        }
    }


    function uploadImageInDB($fileName, $targetFile, $imageFileType)
    {
        if (!(move_uploaded_file($fileName, $targetFile)))
            header("Location:../admin/admin_panel.php?error=errorUpload");

        else
        {
            $this->sqlService->insertData('image', array(
                array(
                    'name_image' => $_FILES['pictureToUpload']["name"],
                    'extension' => $imageFileType,
                    'price' => $_POST['price']
                )));
            $this->insertKeywordsInDB();
            $this->add_copyright($_FILES['pictureToUpload']['name'], $imageFileType);
            header("Location:../admin/admin_panel.php?error=noError");
        }
    }

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


    function insertKeywordsInDB()
    {
        $imageID = $this->sqlService->getData('image', 'id_image', array("where" => "name_image = '".$_FILES["pictureToUpload"]["name"]."'"));
        $keywordArray = $this->keywordsSelected();
        $imageID = $this->extractValueFromArray($imageID);

        foreach ($keywordArray as $key => $keyword)
        {
            $this->sqlService->insertData('image_keyword', array(
                array(
                    'id_image' => $imageID,
                    'id_keyword' => $keyword,
                )));
        }
    }

    function keywordsSelected()
    {
        $checkBoxesName = $this->sqlService->getData('keyword', 'id_keyword');
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


    function extractValueFromArray($array)
    {
        foreach ($array as $key => $value)
        {
            foreach ($value as $key_value => $value_value)
                return $value_value;
        }
    }



    function deleteKeywordAssociatedToImage($arrayImageToDelete)
    {
        foreach ($arrayImageToDelete as $key => $imageSelected)
        {
            $idStringLength = stripos($imageSelected, '._image'); //Find the 'id' string position in the image name
            $imageSelected = substr($imageSelected, 0,$idStringLength); //Delete the 'id' attribute string from the image name

            $idImage = $this->sqlService->getData('image', 'id_image', array("where" =>"name_image = '$imageSelected'"));
            $idImage = $this->extractValueFromArray($idImage);

            $this->sqlService->removeData('image_keyword',"id_image = '$idImage' ", 1);

        }
    }

}