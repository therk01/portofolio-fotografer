<?php
include "../db.php"; // Menghubungkan ke database

// Proses untuk menambah foto
if (isset($_POST['add_photo'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Proses upload gambar
    $target_dir = "../uploads/"; // Folder untuk menyimpan gambar
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar yang valid
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        echo "<script>alert('File is not an image.');</script>";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["image"]["size"] > 500000) { // 500KB limit
        echo "<script>alert('Sorry, your file is too large.');</script>";
        $uploadOk = 0;
    }

    // Cek format file
    if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        $uploadOk = 0;
    }

    // Cek jika $uploadOk di-set ke 0 oleh kesalahan
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.');</script>";
    } else {
        // Jika semuanya baik, coba upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO photos (title, description, image_url) VALUES ('$title', '$description', '$target_file')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Photo added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding photo: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    }
}

// Proses untuk menghapus foto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM photos WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Photo deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting photo: " . $conn->error . "');</script>";
    }
}

// Proses untuk mengupdate foto
if (isset($_POST['update_photo'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url']; // URL gambar yang sudah ada

    // Jika ada file baru yang diupload
    if ($_FILES["image"]["name"]) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file; // Update URL gambar
    }

    $sql = "UPDATE photos SET title='$title', description='$description', image_url='$image_url' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Photo updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating photo: " . $conn->error . "');</script>";
    }
}

// Mengambil data foto dari database
$sql = "SELECT * FROM photos";
$result = $conn->query($sql);
?>

<html lang="en">
<head>
<meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Photo Gallery Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2">
            <div class="text-2xl font-semibold text-center">Admin Dashboard</div>
            <nav>
                <a class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white" href="dashboard.php">
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Photo Gallery Management</h1>
                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="document.getElementById('addModal').style.display='block'">
                    <i class="fas fa-plus"></i> Add New Photo
                </button>
            </div>

            <!-- Modal untuk menambah atau mengupdate foto -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2 class="text-lg font-semibold">Add or Update Photo</h2>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="mb-4">
                            <label class="block text-gray-600" for="title">Title</label>
                            <input class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" id="title" name="title" type="text" required/>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-600" for="description">Description</label>
                            <textarea class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-600" for="image">Image</label>
                            <input class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" id="image" name="image" type="file" accept="image/*" required/>
                        </div>
                        <button class="bg-blue-600 text-white py-2 px-4 rounded" type="submit" name="add_photo">
                            Add Photo
                        </button>
                        <button class="bg-green-600 text-white py-2 px-4 rounded" type="submit" name="update_photo">
                            Update Photo
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="bg-white p-6 rounded-lg shadow-lg">';
                        echo '<img alt="' . $row['title'] . '" class="w-full h-48 object-cover rounded-lg" src="' . $row['image_url'] . '" />';
                        echo '<div class="mt-4 flex justify-between items-center">';
                        echo '<h2 class="text-lg font-semibold">' . $row['title'] . '</h2>';
                        echo '<div>';
                        echo '<button class="text-yellow-500 hover:text-yellow-700" onclick="editPhoto(' . $row['id'] . ', \'' . $row['title'] . '\', \'' . $row['description'] . '\', \'' . $row['image_url'] . '\')"><i class="fas fa-edit"></i></button>';
                        echo '<a href="?delete=' . $row['id'] . '" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>';
                        echo '</div>';
                        echo '</div>';
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

    <script>
        function editPhoto(id, title, description, image_url) {
            document.getElementById('addModal').style.display = 'block';
            document.querySelector('input[name="id"]').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            document.getElementById('image_url').value = image_url; // Jika Anda ingin menampilkan URL gambar
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('addModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>