<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT User_id, Fname, Lname, Password, Role FROM users WHERE Email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['Password'])) {
                // Debug logging
                error_log('Login successful for email: ' . $email);
                error_log('User role from DB: ' . $user['Role']);
                
                $_SESSION['user_id'] = $user['User_id'];
                $_SESSION['user_name'] = $user['Fname'] . ' ' . $user['Lname'];
                $_SESSION['user_type'] = strtolower($user['Role']);
                
                // Debug logging
                error_log('Session after login: ' . print_r($_SESSION, true));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'role' => strtolower($user['Role'])
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Something went wrong'
        ]);
    }
    
    $stmt->close();
}

$conn->close();
?>
