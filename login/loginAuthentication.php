<?php
include '../dbcon.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, PUT, DELETE, POST');

$server_method = $_SERVER['REQUEST_METHOD'];

if ($server_method == 'POST') {
    // Get username and password from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user data
    $sql = "SELECT * FROM tbl_user WHERE username = ? AND pass = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        // User found, return user data
        echo json_encode($users[0]); // Assuming only one user matches
    } else {
        // User not found, return error message
        error404("Invalid username or password.");
    }
} else {
    error422("Invalid request method.");
}

function error422($message)
{
    $response = [
        'status' => 422,
        'message' => $message,
    ];
    header('HTTP/1.0 422 Invalid Entity');
    echo json_encode($response);
    exit();
}

function error404($message)
{
    $response = [
        'status' => 404,
        'message' => $message,
    ];
    header('HTTP/1.0 404 Not Found');
    echo json_encode($response);
    exit();
}
?>
