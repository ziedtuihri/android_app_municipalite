<?php

$con = mysqli_connect("mysql", "root", "password", "database") or die("Couldn't make connection.");

$mysqli = new mysqli("mysql", "root", "password", "database");


// select all table from your database.
$sql_Full_Tables = "SELECT table_name FROM information_schema.tables WHERE table_schema ='database' AND TABLE_TYPE = 'BASE TABLE'";

/* error 1067 when add id_tenant not added and when create view is an error
par defeaut sql_mode = ONLY_FULL_GROUP_BY,​STRICT_TRANS_TABLES,​NO_ZERO_IN_DATE,​NO_ZERO_DATE,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
dans cet cas il ya un erreur dans la creation des 3 view members et logs et ..... alors changé sql_mode = ''; par vide

par default
ONLY_FULL_GROUP_BY,​STRICT_TRANS_TABLES,​NO_ZERO_IN_DATE,​NO_ZERO_DATE,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
*/

$mysqli->query("SET SESSION sql_mode = '';");
$mysqli->query("SET GLOBAL sql_mode = '';");


// add privelege for each tanant user.
$mysqli_Privileg = "REVOKE ALL PRIVILEGES ON *.* FROM 'user1'@'%'; REVOKE GRANT OPTION ON *.* FROM 'user1'@'%'; GRANT SELECT, INSERT, UPDATE, DELETE, FILE, LOCK TABLES ON *.* TO 'user1'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;";

$sql_result1 = mysqli_query($con, $sql_Full_Tables) or die("execute query 1 All Tables!");
//$sql_result2 = mysqli_query($con, $mysqli_Privileg) or die("execute quwhere ery 2 All privilege");


while ($row1 = mysqli_fetch_array($sql_result1)) {
    echo "<br> :: " . $row1['table_name'];

    $tab = explode('_', $row1['table_name'], 2);
    $TableName = $tab[0];

    echo "<br>old : => " . $tab[0] . " <br> new : " . $tab[1];

    if ($TableName == "prefix") {
        $View_Name = $tab[1];
        $TableName = $row1['table_name'];
    } else {
        $View_Name = $row1['table_name'];
        $TableName = "prefix_" . $row1['table_name'];
        if ($mysqli->query("RENAME TABLE " . $View_Name . " TO " . $TableName)) {

        } else {
            printf("Échec : %s\n", $mysqli->error);

        }
    }

    // test sur l'ajout des id_tenant sur chaque table
    if ($result = $mysqli->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $TableName . "' AND COLUMN_NAME = 'id_tenant' ORDER BY ORDINAL_POSITION")) {

        if ($result->num_rows == 0) {
            //si il n ya pas des id_tenant  les ajouté
            $mysqli->query("ALTER TABLE " . $TableName . " ADD COLUMN id_tenant VARCHAR(65) NOT NULL DEFAULT 'ROOT' ;");
        } else echo "<br>tenant exist " . $result->num_rows;

    } else {
        printf("Échec when add COLUMN id_tenant : %s\n", $mysqli->error);
    }

    $trigger_Name = "bi_" . $TableName;

    $mysqli->query("DROP TRIGGER IF EXISTS" . $trigger_Name);

    if ($mysqli->query("CREATE TRIGGER " . $trigger_Name . " BEFORE INSERT ON " . $TableName . " FOR EACH ROW thisTrigger: BEGIN IF (SUBSTRING_INDEX(USER(),'@',1) = 'root') THEN LEAVE thisTrigger; END IF; SET new.id_tenant = SUBSTRING_INDEX(USER(),'@',1); END")) {

    } else {
        printf("Échec : CREATE TRIGGER  %s\n", $mysqli->error);
    }


    $all_columns = [];
    if ($sql_Columns = $mysqli->query("SHOW COLUMNS FROM " . $TableName)) {
        foreach ($sql_Columns as $columnInfo) {
            $fieldName = "`" . $columnInfo['Field'] . "`";
            if ($fieldName != 'id_tenant')
                array_push($all_columns, $fieldName);
            echo "<br>column ->" . $fieldName;
        }

        print_r($all_columns);
        $fieldList = implode(",", $all_columns);
        echo "<br><h4> create View : " . $View_Name . "  => table ->" . $TableName . $fieldList . "</h4>";
    } else {
        printf("<br>Échec SHOW COLUMNS : %s\n", $mysqli->error);
    }

    //select a name for all creating View.
    $nameView = $View_Name;
    //creating Views.
    $query = "CREATE OR REPLACE VIEW `" . $nameView . "` AS SELECT " . $fieldList . " FROM `" . $TableName . "` WHERE (id_tenant = SUBSTRING_INDEX(USER( ),'@',1));";

    $stmt = $mysqli->prepare($query);

    if (!$stmt->execute()) {
        $arr = $stmt->errorInfo();
        print_r($arr);
        echo "error when create Views" . $mysqli->error;
    }

}
//finally your database is genereted next create users and ensure they only have access to our views.

// select all Tables and Views.
$sql_ViewAndTables = "SHOW FULL TABLES";

// group all tables/views into two buckets, allowed & banned
$allowedTables = [];
$bannedTables = [];

if ($sql_ViewAnd_Tables = $mysqli->query("SHOW FULL TABLES")) {
    foreach ($sql_ViewAnd_Tables as $ViewTable) {
        if($ViewTable['Table_type'] != "BASE TABLE"){
            // this is a view, it's ok this work basic with Views.
            array_push($allowedTables ,$ViewTable['Tables_in_database']);
        }else {
            array_push($bannedTables ,$ViewTable['Tables_in_database']);
        }
    echo "<br>testing this query ".$ViewTable['Tables_in_database'];
    }

}else {
    printf("<br>Échec group all tables/views : %s\n", $mysqli->error);
}

// create the user
$USER = "user3";
$mysqli->query("CREATE USER `".$USER."` IDENTIFIED BY 'password';");

// grant them access
$mysqli->query("GRANT LOCK TABLES ON *.* TO '".$USER."'@'%';");

foreach($allowedTables as $table){
    $mysqli->query("GRANT SELECT, UPDATE, INSERT, DELETE ON database.".$table." TO `".$USER."`");
}

foreach($bannedTables as $table){
    $mysqli->query("REVOKE ALL ON database.".$table." FROM `".$USER."`");
}

$mysqli->query("FLUSH PRIVILEGES");
?>