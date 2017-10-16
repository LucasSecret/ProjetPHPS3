<?php

// TODO: PHPDoc and CamelCase
class SQLServices
{
    private $db;

    function __construct($host, $dbname, $user, $password) {
        try {
            $this->db =  new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password);
        } catch (Exception $e) {
            die('Error : ' . $e->getMessage());
        }
    }

    /**
     * @param $array
     * @return string with ', ' separation
     */
    static private function getSQLStringFromArray($array) {
        if (!is_array($array))
            return $array . " ";

        $sqlString = "";
        foreach ($array as $value)
            $sqlString .= $value . ", ";

        return substr($sqlString, 0, -2) . " ";
    }

    /**
     * @param $array
     * @return string with formatted for SQL insert
     */
    static private function formatDataForValueInsertion($array) {
        $sqlString = "(";
        foreach ($array as $value) {
            if (is_string($value))
                $sqlString .= "'$value'";
            else
                $sqlString .= $value;
            $sqlString .= ", ";
        }

        return substr($sqlString, 0, -2) . ")";
    }

    /**
     * @param $array
     * @return string with formatted for SQL insert
     */
    static private function formatDataForKeyInsertion($array) {
        $sqlString = "";
        foreach ($array as $key => $value)
            $sqlString .= $key . ", ";

        return substr($sqlString, 0, -2) . ") ";
    }

    /**
     * @param $table
     * @param $select
     * @param null $options : where, group_by, limit, order_by
     *
     * @return data matching the request
     */
    function getData($table, $select, $options = null)
    {
        $query = "SELECT " . SQLServices::getSQLStringFromArray($select);
        $query .= "FROM $table ";

        if (isset($options)) {
            if (array_key_exists("where", $options)) {
                $query .= "WHERE " . $options["where"] . " ";
            }
            if (array_key_exists("group_by", $options)) {
                $query .= "GROUP BY " . SQLServices::getSQLStringFromArray($options["group_by"]);
            }
            if (array_key_exists("limit", $options)) {
                $query .= "LIMIT " . $options["limit"] . " ";
            }
            if (array_key_exists("order_by", $options)) {
                $query .= "ORDER BY " . SQLServices::getSQLStringFromArray($options["order_by"]);
            }
        }

        $cursor = $this->db->query($query);
        if ($cursor == false) {
            return null;
        }
        $result = $cursor->fetchAll(PDO::FETCH_ASSOC);
        $cursor->closeCursor();
        return $result;
    }

    function insertData($table, $values)
    {
        foreach($values as $value) {

            if (!is_array($value))
                continue;

            $query = "INSERT INTO $table(";

            $query .= self::formatDataForKeyInsertion($value);
            $query .= "VALUES";

            $query .= self::formatDataForValueInsertion($value);

            $this->db->exec($query);
        }
    }

    function removeData($table, $optionWhere, $limit) {
        $query = "DELETE FROM $table ";

        if (isset($optionWhere)) {
            $query .= "WHERE $optionWhere ";
        }
        if (isset($limit)) {
            $query .= "LIMIT $limit";
        }

        $this->db->exec($query);
    }

    function updateData($table, $optionWhere, $value) {
        $query = "UPDATE $table ";
        $query .= "SET $value ";

        if (isset($optionWhere)) {
            $query .= "WHERE $optionWhere";
        }

        $this->db->exec($query);
    }

    function displayData($table, $select, $options = null) {
        $data = $this->getData($table, $select, $options);

        if (isset($data)) {
            echo "<table>";
            foreach ($data as $row) {
                echo '<tr>';
                foreach($row as $value) {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
                echo '</tr>';
            }
            echo "</table>";
        }
        else {
            echo "Data Not Found";
        }
    }

    
    function isAdmin($username, $password)
    {
        $statement = "SELECT count(*) FROM user ";
        $statement .= "WHERE username = '".$username."' ";
        $statement .= "AND password = '".md5($password)."' ";
        $statement .= "AND admin = 1";
        echo $statement;

        $query = $this->db->query($statement);

        if ($query->fetchColumn() == 0)
            return false;

        return true;
    }

    function isRegistered($username, $password)
    {
        $statement = "SELECT count(*) FROM user ";
        $statement .= "WHERE username = '".$username."' ";
        $statement .= "AND password = '".md5($password)."' ";
        $statement .= "AND admin = 0";
        echo $statement;
        $query = $this->db->query($statement);

        if ($query->fetchColumn() == 0)
            return false;

        return true;
    }


    function displayImageWithKeyword($idKeywords = null)
    {

        if(!empty($idKeywords))
        {
            $tableJoin = "image i JOIN image_keyword ik ON i.id_image = ik.id_image
                            JOIN keyword k ON ik.id_keyword = k.id_keyword";

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

                $imagesName = $this->getData($tableJoin, 'distinct name_image', $optionsArray);

                if(is_array($imagesName))
                {
                    foreach ($imagesName as $key => $line)
                    {
                        foreach ($line as $column => $imageName){
                            echo "<img src=\"../images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";
                        }
                    }
                }

            }

            else //If there is only one keyword given in parameters
            {
                $optionsArray = ["where" => "ik.id_keyword = $idKeywords"];

                $imagesName = $this->getData($tableJoin, 'name_image', $optionsArray);

                if(is_array($imagesName)) //If there are several images returned by the query
                {
                    foreach ($imagesName as $key => $line)
                    {
                        foreach ($line as $column => $imageName){
                            echo "<img src=\"../images_copyright/$imageName\" alt=\"$imageName\" id=\"$imageName._image\" >";
                        }
                    }
                }

                else
                {
                    echo "<img src=\"../images_copyright/$imagesName\" alt=\"$imagesName\" id=\"$imagesName._image\" >";
                }

            }


        }

        else //If no keywords in parameters
        {
            $imageName = $this->getData('image', 'name_image');


            if (!is_null($imageName)) {
                foreach ($imageName as $key => $line)
                {
                    foreach ($line as $column => $value_column)
                        echo "<img src=\"../images_copyright/$value_column\" alt=\"$value_column\" id=\"$value_column._image\" >";
                }
            }
        }
    }


    function displayCheckbox()
    {
        $checkBoxesName = $this->getData('keyword', 'id_keyword, name_keyword');

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
                echo "<input type=\"checkbox\" name=" . "\"$id" . "_checkbox\" > $keyword";
            }
        }

    }



}