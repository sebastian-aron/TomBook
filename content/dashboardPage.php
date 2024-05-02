<?php
include '../dbcon.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, PUT, DELETE, POST');

$server_method = $_SERVER['REQUEST_METHOD'];

if ($server_method == 'GET') {
    // Check if search query parameters are provided
    if(isset($_GET['search'])){
        $searchQuery = $_GET['search'];
        $sql = "SELECT * FROM tbl_content WHERE title LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%' OR author LIKE '%$searchQuery%' OR category LIKE '%$searchQuery%'";
    } else {
        $sql = "SELECT * FROM tbl_content";
    }

    $stmt = $conn->query($sql);

    // Check if the query was successful
    if ($stmt) {
        // Initialize an empty array to hold the data
        $book_lists = array();

        // Loop through the query result
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Push the row to the array
            $book_lists[] = $row;
        }

        // Encode the data to JSON and output it
        echo json_encode($book_lists);
    } else {
        error422("Failed to fetch data from the database.");
    }
    
} else if ($server_method == 'POST') {
    // Read raw input data
    $input_data = file_get_contents('php://input');
    // Parse raw input data
    $post_data = json_decode($input_data, true);

    // Get form data
    $title = $post_data['title'];
    $description = $post_data['description'];
    $author = $post_data['author'];
    $category = $post_data['category'];

    // Insert data into the table using prepared statements
    $stmt = $conn->prepare("INSERT INTO tbl_content (title, description, author, category) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $author, $category])) {
        $response = [
            'status' => 200,
            'message' => 'Content Added Successfully',
        ];
        echo json_encode($response);
    } else {
        error422('Failed to add content');
    }
    
} else if ($server_method == 'PUT') {
    // Get the PUT data
    $putData = file_get_contents('php://input');
    $data = json_decode($putData, true);

    // Check if required fields are present
    if (!isset($data['id'])) {
        error422('ID not found');
    }

    // Extract data from the PUT request
    $id = $data['id'];
    $title = $data['title'];
    $description = $data['description'];
    $author = $data['author'];
    $category = $data['category'];

    // Update the corresponding record in the table
    $stmt = $conn->prepare("UPDATE tbl_content SET title=?, description=?, author=?, category=? WHERE id=?");
    if ($stmt->execute([$title, $description, $author, $category, $id])) {
        $response = [
            'status' => 200,
            'message' => 'Content Updated Successfully',
        ];
        echo json_encode($response);
    } else {
        error422('Failed to update content');
    }
    
} else if ($server_method == 'DELETE') {
    // Get the ID of the content to be deleted
    $id = $_GET['id'];

    // Delete the corresponding record from the table
    $stmt = $conn->prepare("DELETE FROM tbl_content WHERE id=?");
    if ($stmt->execute([$id])) {
        $response = [
            'status' => 200,
            'message' => 'Content Deleted Successfully',
        ];
        echo json_encode($response);
    } else {
        error422('Failed to delete content');
    }
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
?>
