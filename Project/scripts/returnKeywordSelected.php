<?php

include('../classes/SQLServices.php');
include('../includes/variables.inc.php');

$sqlService = new SQLServices($hostnameDB, $dbName, $userDB, $passwordDB);
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

header("Location:../user/userIndex.php?keywords=$keywordsSelected");
?>