<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = $_POST['matric'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE matric = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['matric'] = $user['matric'];
            $_SESSION['name'] = $user['name']; // Store the name in the session
            $_SESSION['accessLevel'] = $user['accessLevel'];

            // Redirect to dashboard with a success message
            header("Location: dashboard.php?welcome=true&name=" . urlencode($user['name']));
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container">
        <h2>User Login</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form method="post" action="">
            <input type="text" name="matric" placeholder="Matric Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
