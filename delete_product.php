<?php
/* start session and include connection */
session_start();
include('includes/db_conn.php');

/* security check: only logged in users can delete */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* get the product ID from the URL link */
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    /* SQL command to remove the product */
    $sql = "DELETE FROM products WHERE id = $product_id";

    if (mysqli_query($conn, $sql)) {
        /* go back to products page with success message */
        header("Location: products.php?msg=Product deleted successfully");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    /* if no ID found, just go back */
    header("Location: products.php");
}
exit();
?>