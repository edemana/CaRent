<?php
session_start();
require_once '../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$msg = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE User_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Handle file upload
    $profile_image = $user['Profile_image'] ?? ''; // Keep existing image by default
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $msg = '<div class="alert alert-danger">Only JPG, PNG and GIF images are allowed.</div>';
        } elseif ($file['size'] > $maxSize) {
            $msg = '<div class="alert alert-danger">File size must be less than 5MB.</div>';
        } else {
            // Generate unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $uploadPath = '../uploads/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Delete old profile image if exists
                if (!empty($user['Profile_image']) && file_exists('../uploads/' . $user['Profile_image'])) {
                    unlink('../uploads/' . $user['Profile_image']);
                }
                $profile_image = $filename;
            } else {
                $msg = '<div class="alert alert-danger">Failed to upload image. Please try again.</div>';
            }
        }
    }

    // If no upload errors, proceed with profile update
    if (empty($msg)) {
        // Validate inputs
        if (empty($name) || empty($email)) {
            $msg = '<div class="alert alert-danger">Name and email are required fields.</div>';
        } else {
            // Check if email already exists for other users
            $stmt = $conn->prepare("SELECT User_id FROM users WHERE Email = ? AND User_id != ?");
            $stmt->bind_param("si", $email, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $msg = '<div class="alert alert-danger">Email already exists.</div>';
            } else {
                // Update profile with image
                $stmt = $conn->prepare("UPDATE users SET Name = ?, Email = ?, Phone = ?, Address = ?, Profile_image = ? WHERE User_id = ?");
                $stmt->bind_param("sssssi", $name, $email, $phone, $address, $profile_image, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['user_name'] = $name; // Update session name
                    $msg = '<div class="alert alert-success">Profile updated successfully!</div>';
                    // Refresh user data
                    $stmt = $conn->prepare("SELECT * FROM users WHERE User_id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                } else {
                    $msg = '<div class="alert alert-danger">Error updating profile. Please try again.</div>';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CarRent</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            display: none;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-header h1 {
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            color: #636e72;
        }

        .profile-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: grid;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: #2d3436;
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #0984e3;
            box-shadow: 0 0 0 2px rgba(9, 132, 227, 0.1);
            outline: none;
        }

        .submit-btn {
            background: #0984e3;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #0770c2;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0984e3;
            background: #f5f6fa;
        }

        .profile-image-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #0984e3;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-image-upload:hover {
            background: #0770c2;
            transform: scale(1.1);
        }

        .profile-image-upload i {
            color: white;
            font-size: 1.2rem;
        }

        .profile-image-upload input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        #imagePreview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="profile-container">
        <?php echo $msg; ?>
        
        <div class="profile-header">
            <div class="profile-image-container">
                <?php if (!empty($user['Profile_image']) && file_exists('../uploads/' . $user['Profile_image'])) : ?>
                    <img src="../uploads/<?php echo htmlspecialchars($user['Profile_image']); ?>" alt="Profile" class="profile-image" id="imagePreview">
                <?php else : ?>
                    <img src="../images/default-avatar.png" alt="Default Profile" class="profile-image" id="imagePreview">
                <?php endif; ?>
                <div class="profile-image-upload">
                    <i class="fas fa-camera"></i>
                    <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this)">
                </div>
            </div>
            <h1>My Profile</h1>
            <p>Update your personal information</p>
        </div>

        <form class="profile-form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>">
            </div>

            <button type="submit" class="submit-btn">Update Profile</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
