<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $newStatus = $_POST['status'];
    
    // Thực hiện cập nhật trạng thái trong CSDL
    $update_query = "UPDATE project_list SET status = $newStatus WHERE id = $id";
    if ($conn->query($update_query)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'Invalid request';
}
?>
