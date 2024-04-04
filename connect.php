<?php
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL);
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
$host = $argv[1];
$username = $argv[2];
$password = $argv[3];
$port = 3306;
$query = $argv[4];

$conn = mysqli_connect($host, $username, $password);
if ($conn->connect_errno) {
    $data = [
        'code' => 500,
        'message' => "Connect Fail: " . $conn->connect_error,
        'data' => null
    ];
    echo json_encode($data);
    exit();
} else {
    if($query == null) {
        $data = [
            'code' => 200,
            'message' => "Connect Success",
            'data' => null
        ];
        echo json_encode($data);
        $conn->close();
        exit();
    } else {
        $result = $conn->query($query);
        $dataRow = [];
        while ($row = $result->fetch_assoc()) {
            $dataRow[] = $row;
        }
        $data = [
            'code' => 200,
            'data' => $dataRow
        ];
        echo json_encode($data);
        $conn->close();
    }
}
?>
