<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:uniemprende-server.database.windows.net; Database = uniemprendeDB", "adminsql", "<Loscapis>");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "adminsql", "pwd" => "<Loscapis>", "Database" => "uniemprendeDB", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:uniemprende-server.database.windows.net";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>