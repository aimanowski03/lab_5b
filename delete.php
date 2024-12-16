<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['matric'])) {
    header("Location: login.php");
    exit();
}

// Check if matric is provided
if (!isset($_GET['matric'])) {
    header("Location: dashboard.php");
    exit();
}

$matric = $_GET['matric'];

// Only allow admin or the user themselves to delete
if ($_SESSION['accessLevel'] != 'admin' && $_SESSION['matric'] != $matric) {
    header("Location: dashboard.php");
    exit();
}

// Prevent deleting the last admin
$admin_count_query = "SELECT COUNT(*) as admin_count FROM users WHERE accessLevel = 'admin'";
$admin_count_result = $conn->query($admin_count_query);
$admin_count = $admin_count_result->fetch_assoc()['admin_count'];

// If this is the last admin and the user is an admin
if ($admin_count <= 1 && $_SESSION['accessLevel'] == 'admin') {
    $_SESSION['error'] = "Cannot delete the last admin user.";
    header("Location: dashboard.php");
    exit();
}

// Delete the user
$delete_query = "DELETE FROM users WHERE matric = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("s", $matric);

if ($stmt->execute()) {
    if ($_SESSION['matric'] == $matric) {
        // User deletes themselves
        echo "
            <script>
                alert('Deletion successful. You will now be logged out.');
                window.location.href = 'logout.php';
            </script>
        ";
    } else {
        // Admin deletes another user
        echo "
            <script>
                alert('User deleted successfully. Redirecting to the dashboard.');
                window.location.href = 'dashboard.php';
            </script>
        ";
    }
    exit();
} else {
    $_SESSION['error'] = "Error deleting user: " . $stmt->error;
    header("Location: dashboard.php");
    exit();
}
?>
