<?php
if(isset($_GET['file'])){
    $file_to_delete = $_GET['file'];
    
    // Kết nối đến cơ sở dữ liệu
    include 'db_connect.php';
    
    // Xóa tệp khỏi cơ sở dữ liệu
    $delete_query = "UPDATE task_list SET file_name = TRIM(BOTH ',' FROM REPLACE(file_name, '$file_to_delete', '')) WHERE FIND_IN_SET('$file_to_delete', file_name)";
    
    if($conn->query($delete_query)){
        // Xóa tệp từ thư mục lưu trữ
        $file_path = "assets/uploads/task_documents/" . $file_to_delete;
        if(file_exists($file_path)){
            unlink($file_path);
        }
        
        // Xóa dấu phẩy thừa nếu có
        $fix_commas_query = "UPDATE task_list SET file_name = TRIM(BOTH ',' FROM REPLACE(file_name, ',,', ',')) WHERE file_name LIKE '%,,%' OR file_name LIKE ',%'";
        $conn->query($fix_commas_query);
        
        // Đóng kết nối đến cơ sở dữ liệu
        $conn->close();

        // Chuyển hướng người dùng trở lại trang trước đó
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Lỗi xóa tệp khỏi cơ sở dữ liệu.";
    }
}
?>
