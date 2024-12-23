<!-- update_restaurant_info.php -->
<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_id = $_SESSION['restaurant_id'];
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $working_hours = $conn->real_escape_string($_POST['working_hours']);
    $google_map_link = $conn->real_escape_string($_POST['google_map_link']);
    $image = $_FILES['restaurant_image'];

    if ($image['error'] == 0) {
        $imageName = time() . '-' . $image['name'];
        $imagePath = '../uploads/' . $imageName;
        move_uploaded_file($image['tmp_name'], $imagePath);
    } else {
        $_SESSION['error_message'] = "خطأ في تحميل الصورة";
        header('Location: manage_products.php');
        exit();
    }

    $sql = "UPDATE restaurants SET phone='$phone', address='$address', working_hours='$working_hours', google_map_link='$google_map_link', image='$imageName' WHERE id=$restaurant_id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "تم تحديث معلومات المطعم بنجاح";
    } else {
        $_SESSION['error_message'] = "خطأ: " . $sql . "<br>" . $conn->error;
    }

    header('Location: manage_products.php');
    exit();
}

$conn->close();
?>
