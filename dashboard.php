<?php
require_once 'config.php';

// Start the session only if it hasn't already been started
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Check if user is logged in
if (!isset($_SESSION['matric'])) { header("Location: login.php"); exit(); }

// Fetch all users, including the role
$query = "SELECT matric, name, role FROM users";  // Change accessLevel to role
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fc; margin: 0; padding: 0; }
        header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .logout { background: #333; color: white; padding: 10px; text-align: right; }
        .logout a { color: #ffcc00; text-decoration: none; font-weight: bold; }
        .logout a:hover { text-decoration: underline; }
        .container { padding: 30px; margin: 20px auto; max-width: 1200px; background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        a { color: #007bff; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
        .admin { color: #ff5722; font-weight: bold; }
        .modal { display: none; position: fixed; z-index: 1; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); padding-top: 60px; }
        .modal-content { background: #fff; margin: auto; padding: 30px; border-radius: 8px; width: 80%; max-width: 500px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .close { float: right; font-size: 28px; cursor: pointer; }
        .close:hover { color: black; }
    </style>
    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['name'])): ?>
                document.getElementById("welcomeModal").style.display = "block";
                document.getElementsByClassName("close")[0].onclick = function() { document.getElementById("welcomeModal").style.display = "none"; }
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <header><h1>User Dashboard</h1></header>
    <div class="logout">
        <p>Welcome, <?php echo $_SESSION['name']; ?> [<a href="logout.php">Logout</a>]</p>
    </div>
    <div class="container">
        <h2>User List</h2>
        <table>
            <thead>
                <tr><th>Matric Number</th><th>Name</th><th>Access Level</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['matric']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td class="<?php echo ($row['role'] == 'admin') ? 'admin' : ''; ?>"><?php echo $row['role']; ?></td> <!-- Display role instead of accessLevel -->
                    <td>
                        <?php if ($_SESSION['accessLevel'] == 'admin' || $_SESSION['matric'] == $row['matric']): ?>
                            <a href="update.php?matric=<?php echo $row['matric']; ?>">Update</a>
                            <a href="delete.php?matric=<?php echo $row['matric']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="welcomeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
        </div>
    </div>
</body>
</html>
