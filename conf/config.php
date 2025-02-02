<?php 

if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    http_response_code(404);
    die('404 Not Found');
}

$host = ''; // Database host
$charDbName = 'acore_characters'; // Characters database name
$authDbName = 'acore_auth';
$worldDbName = 'acore_world'; // World database name
$user = ''; // Database username
$pass = ''; // Database password

// Initialize connection variable
$conn = null;

try {
    $conn = new mysqli($host, $user, $pass, $authDbName);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    $conn = null;
}

function safeQuery($sql) {
    global $conn;
    if ($conn) {
        return $conn->query($sql);
    } else {
        return false;
    }
}

?>