<?php
/**
 * Created by PhpStorm.
 * User: r00tme
 * Date: 4/9/2019
 * Time: 5:12 PM
 */

if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {header("Location:/");}else{

    $sql_host                     = '';           // Sql server host: 127.0.0.1,localhost,Your Computer Name
    $sql_user                     = '';           // Sql server user: sa
    $sql_pass                     = '';           // Sql server password
    $sql_database                 = '';

    if (!class_exists('mssqlQuery')) {
        class mssqlQuery
        {
            private $data = array();
            private $rowsCount = 0;
            private $fieldsCount = null;

            public function __construct($resource)
            {
                if ($resource) {
                    while ($data = sqlsrv_fetch_array($resource)) {
                        $this->addData($data);
                    }

                    sqlsrv_free_stmt($resource);
                }
            }

            public function getRowsCount()
            {
                return $this->rowsCount;
            }

            public function getFieldsCount()
            {
                if ($this->fieldsCount === null) {
                    $this->fieldsCount = 0;
                    $data = reset($this->data);

                    if ($data) {
                        foreach ($data as $key => $value) {
                            if (is_numeric($key)) {
                                $this->fieldsCount++;
                            }
                        }
                    }
                }

                return $this->fieldsCount;
            }

            private function addData($data)
            {
                $this->rowsCount++;
                $this->data[] = $data;
            }

            public function getData()
            {
                return $this->data;
            }

            public function shiftData($resultType = SQLSRV_FETCH_BOTH)
            {
                $data = array_shift($this->data);

                if (!$data) {
                    return false;
                }

                if ($resultType == SQLSRV_FETCH_NUMERIC) {
                    foreach ($data as $key => $value) {
                        if (!is_numeric($key)) {
                            unset($data[$key]);
                        }
                    }
                } else {
                    if ($resultType == SQLSRV_FETCH_ASSOC) {
                        foreach ($data as $key => $value) {
                            if (is_numeric($key)) {
                                unset($data[$key]);
                            }
                        }
                    }
                }

                return $data;
            }
        }
    }


    if (!function_exists('mssql_connect')) {
        function mssql_connect($servername, $username, $password, $newLink = false)
        {
            if (empty($GLOBALS['_sqlsrvConnection'])) {
                $connectionInfo = array(
                    "CharacterSet" => "UTF-8",
                    "UID" => $username,
                    "PWD" => $password,
                    "ReturnDatesAsStrings" => true
                );

                $GLOBALS['_sqlsrvConnection'] = sqlsrv_connect($servername, $connectionInfo);

                if ($GLOBALS['_sqlsrvConnection'] === false) {
                    foreach (sqlsrv_errors() as $error) {
                        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                        echo "code: " . $error['code'] . "<br />";
                        echo "message: " . $error['message'] . "<br />";
                    }
                }
            }

            return $GLOBALS['_sqlsrvConnection'];
        }
    }

    if (!function_exists('mssql_pconnect')) {
        function mssql_pconnect($servername, $username, $password, $newLink = false)
        {
            if (empty($GLOBALS['_sqlsrvConnection'])) {
                $connectionInfo = array(
                    "CharacterSet" => "UTF-8",
                    "UID" => $username,
                    "PWD" => $password,
                    "ReturnDatesAsStrings" => true
                );

                $GLOBALS['_sqlsrvConnection'] = sqlsrv_connect($servername, $connectionInfo);

                if ($GLOBALS['_sqlsrvConnection'] === false) {
                    foreach (sqlsrv_errors() as $error) {
                        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                        echo "code: " . $error['code'] . "<br />";
                        echo "message: " . $error['message'] . "<br />";
                    }
                }
            }

            return $GLOBALS['_sqlsrvConnection'];
        }
    }

    if (!function_exists('mssql_close')) {
        function mssql_close($linkIdentifier = null)
        {
            sqlsrv_close($GLOBALS['_sqlsrvConnection']);
            $GLOBALS['_sqlsrvConnection'] = null;
        }
    }

    if (!function_exists('mssql_select_db')) {
        function mssql_select_db($databaseName, $linkIdentifier = null)
        {
            $query = "USE " . $databaseName;

            $resource = sqlsrv_query($GLOBALS['_sqlsrvConnection'], $query);

            if ($resource === false) {
                if (($errors = sqlsrv_errors()) != null) {
                    foreach ($errors as $error) {
                        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                        echo "code: " . $error['code'] . "<br />";
                        echo "message: " . $error['message'] . "<br />";
                    }
                }
            }

            return $resource;
        }
    }

    if (!function_exists('mssql_query')) {
        function mssql_query($query, $linkIdentifier = null, $batchSize = 0)
        {
            if (preg_match('/^\s*exec/i', $query)) {
                $query = 'SET NOCOUNT ON;' . $query;
            }

            $resource = sqlsrv_query($GLOBALS['_sqlsrvConnection'], $query);

            if ($resource === false) {
                if (($errors = sqlsrv_errors()) != null) {
                    foreach ($errors as $error) {
                        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                        echo "code: " . $error['code'] . "<br />";
                        echo "message: " . $error['message'] . "<br />";
                    }
                }
            }

            return new mssqlQuery($resource);
        }
    }

    if (!function_exists('mssql_fetch_array')) {
        function mssql_fetch_array($mssqlQuery, $resultType = SQLSRV_FETCH_BOTH)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            switch ($resultType) {
                case 'MSSQL_BOTH' :
                    $resultType = SQLSRV_FETCH_BOTH;
                    break;
                case 'MSSQL_NUM':
                    $resultType = SQLSRV_FETCH_NUMERIC;
                    break;
                case 'MSSQL_ASSOC':
                    $resultType = SQLSRV_FETCH_ASSOC;
                    break;
            }

            return $mssqlQuery->shiftData($resultType);
        }
    }

    if (!function_exists('mssql_fetch_assoc')) {
        function mssql_fetch_assoc($mssqlQuery)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            return $mssqlQuery->shiftData(SQLSRV_FETCH_ASSOC);
        }
    }

    if (!function_exists('mssql_fetch_row')) {
        function mssql_fetch_row($mssqlQuery)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            return $mssqlQuery->shiftData(SQLSRV_FETCH_NUMERIC);
        }
    }

    if (!function_exists('mssql_num_rows')) {
        function mssql_num_rows($mssqlQuery)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            return $mssqlQuery->getRowsCount();
        }
    }

    if (!function_exists('mssql_num_fields')) {
        function mssql_num_fields($mssqlQuery)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            return $mssqlQuery->getFieldsCount();
        }
    }

    if (!function_exists('mssql_fetch_object')) {
        function mssql_fetch_object($mssqlQuery)
        {
            if (!$mssqlQuery instanceof mssqlQuery) {
                return null;
            }

            return (object)$mssqlQuery->shiftData(SQLSRV_FETCH_ASSOC);
        }
    }

    if (!function_exists('mssql_get_last_message')) {
        function mssql_get_last_message()
        {
            preg_match('/^\[Microsoft.*SQL.*Server\](.*)$/i', sqlsrv_errors(SQLSRV_ERR_ALL), $matches);
            return $matches[1];
        }
    }



    $sql_connect = mssql_connect($sql_host, $sql_user, $sql_pass) or die("Couldn't connect to SQL Server!");
    $db_connect  = mssql_select_db($sql_database, $sql_connect) or die("Couldn't open database: ". $database."");

}

function char_class($value, $view=0)
{
	$class = array(
		0  => array("Dark Wizard","DW"),1 => array("Soul Master","SM"),2 => array("Grand Master","GrM"),3 => array("Grand Master","GrM"),
		16 => array("Dark Knight","DK"),17 => array("Blade Knight","BK"),18 => array("Blade Master","BM"),19 => array("Blade Master","BM"),
		32 => array("Fairy Elf","Elf"),33 => array("Muse Elf","ME"),34 => array("High Elf","HE"),35 => array("High Elf","HE"),
		48 => array("Magic Gladiator"),49 => array("Duel Master","DM"),50 => array("Duel Master","DM"),
		64 => array("Dark Lord","DL"),65 => array("Lord Emperor","LE"),66 => array("Lord Emperor","LE"),
	);
	
	return isset($class[$value][$view]) ? $class[$value][$view] : 'Unknown';   
}

function de_map($value)
{
    $class = array( 0 => 'Lorencia', 1 => "Dungeon", 2 => 'Davias', 3 => 'Noria', 4 => "Lost Tower",  6 => "Arena", 7 => "Atlans", 8 => "Tarkan", 9 => "Devil Square", 10 => "Icarus", 11 => "Blood castle 1", 12 => "Blood castle 2", 13 => "Blood castle 3", 14 => "Blood castle 4", 15 => "Blood castle 5", 16 => "Blood castle 6", 17 => "Blood castle 7", 18 => "Chaos castle 1", 19 => "Chaos castle 2", 20 => "Chaos castle 3", 21=> "Chaos castle 4", 22 => "Chaos castle 5", 23 => "Chaos castle 6",  24 => "Kalima 1", 25 => "Kalima 2", 28 => "Kalima 1", 29 => "Kalima 1",  30 => "Valery Of Loren", 55 => "Valery Of Loren", 31 => "Land of Trial", 54 => "Aida", 33 => "Aida 2", 34 => "Cry Wolf");
    return isset( $class[$value] ) ? $class[$value] : "----";   
}

function pk_level($value)
{      
    switch($value){
		case 1:  return "Hero"; break;
		case 2:  return "Commoner"; break;
		case 3:  return "Normal"; break;
		case 4:  return "Against Murderer"; break;
		case 5:  return "Murderer"; break;
		case 6:  return "Phonomania"; break;
		default: return "Unknown"; break;
    }
}
