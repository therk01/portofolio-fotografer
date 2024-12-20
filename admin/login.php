<?php
session_start(); // Start the session
include "../db.php"; // Include database connection

// Check if the form is submitted
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $sql = "SELECT * FROM admins WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $hashed_password = password_hash('your_password', PASSWORD_DEFAULT);
// Insert into the database
$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hashed_password);
$stmt->execute();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center">Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="text-red-500 text-center mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-gray-600" for="username">Username</label>
                <input class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" id="username" name="username" type="text" required/>
            </div>
            <div class="mb-4">
                <label class="block text-gray-600" for="password">Password</label>
                <input class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" id="password" name="password" type="password" required/>
            </div>
            <button class="bg-blue-600 text-white py-2 px-4 rounded" type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>