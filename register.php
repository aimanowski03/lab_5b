<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = $_POST['matric'];
    $name = $_POST['name'];
    $role = $_POST['role']; // Added role field
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $accessLevel = 'user'; // Default access level

    // Check if matric number already exists
    $check_query = "SELECT * FROM users WHERE matric = ?";
    $stmt = $conn->prepare($check_query);

    // Check if prepare() failed
    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Matric number already exists!";
    } else {
        // Insert new user
        $insert_query = "INSERT INTO users (matric, name, role, password, accessLevel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);

        // Check if prepare() failed
        if ($stmt === false) {
            die("Error preparing the insert query: " . $conn->error);
        }

        $stmt->bind_param("sssss", $matric, $name, $role, $password, $accessLevel);
        
        if ($stmt->execute()) {
            $success = "Registration successful!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <!-- Link to your external CSS file -->
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        if (isset($success)) {
            echo "<p class='success'>$success</p>";
        }
        ?>
        <form method="post" action="">
            <input type="text" name="matric" placeholder="Matric Number" required>
            <input type="text" name="name" placeholder="Name" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="Student">Student</option>
                <option value="Lecturer">Lecturer</option>
            </select>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Submit">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
