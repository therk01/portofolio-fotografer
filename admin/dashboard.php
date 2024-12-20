<?php
include "../db.php"; // Menghubungkan ke database

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Menghitung jumlah foto
$sql = "SELECT COUNT(*) as total_photos FROM photos";
$result = $conn->query($sql);
$total_photos = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_photos = $row['total_photos'];
}

// Mengambil data foto dari database
$sql_photos = "SELECT * FROM photos";
$result_photos = $conn->query($sql_photos);
?>

<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2">
            <div class="text-2xl font-semibold text-center">Admin Dashboard</div>
            <nav>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" href="#">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" href="galery.php">
                    <i class="fas fa-users"></i> Manage Galery
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" href="#">
                    <i class="fas fa-cogs"></i> Settings
                </a>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
        <!-- Main content -->
        <div class="flex-1 p-10">
            <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
            <div class="mt-4">
                <h2 class="text-lg">Total Photos: <?php echo $total_photos; ?></h2>
            </div>

            <div class="mt-6">
                <h2 class="text-lg font-semibold">Photo List</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                    <?php
                    if ($result_photos->num_rows > 0) {
                        while($row = $result_photos->fetch_assoc()) {
                            echo '<div class="bg-white p-6 rounded-lg shadow-lg">';
                            echo '<img alt="' . $row['title'] . '" class="w-full h-48 object-cover rounded-lg" src="' . $row['image_url'] . '" />';
                            echo '<h3 class="mt-4 text-lg font-semibold">' . $row['title'] . '</h3>';
                            echo '<p class="mt-2 text-gray-600">' . $row['description'] . '</p>'; // Menampilkan deskripsi foto
                            echo '</div>';
                        }
                    } else {
                        echo "<p>No photos found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>