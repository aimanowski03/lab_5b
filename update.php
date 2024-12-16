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

// Only allow admin or the user themselves to update
if ($_SESSION['accessLevel'] != 'admin' && $_SESSION['matric'] != $matric) {
    header("Location: dashboard.php");
    exit();
}

// Fetch user details
$query = "SELECT * FROM users WHERE matric = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $matric);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Redirect if no user is found
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $accessLevel = isset($_POST['accessLevel']) ? $_POST['accessLevel'] : $user['accessLevel'];
    $role = isset($_POST['role']) ? $_POST['role'] : $user['role']; // Get the role from the form

    // Update with or without password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET name = ?, password = ?, accessLevel = ?, role = ? WHERE matric = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $password, $accessLevel, $role, $matric);
    } else {
        $query = "UPDATE users SET name = ?, accessLevel = ?, role = ? WHERE matric = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $accessLevel, $role, $matric);
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Update failed: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
            padding: 20px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        form input, form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .back-link {
            margin-top: 15px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update User</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post" action="">
            <input type="text" name="matric" value="<?php echo $user['matric']; ?>" readonly>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
            
            <!-- Optional password field -->
            <input type="password" name="password" placeholder="New Password (optional)">
            
            <!-- Dropdown to select access level -->
            <?php if ($_SESSION['accessLevel'] == 'admin'): ?>
                <select name="accessLevel">
                    <option value="user" <?php echo ($user['accessLevel'] == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user['accessLevel'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            <?php endif; ?>
            
            <!-- Dropdown to select role (Lecturer or Student) -->
            <select name="role">
                <option value="student" <?php echo ($user['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="lecturer" <?php echo ($user['role'] == 'lecturer') ? 'selected' : ''; ?>>Lecturer</option>
            </select>

            <input type="submit" value="Update">
        </form>
        <div class="back-link">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
