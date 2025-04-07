<?php
session_start(); 
require 'connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Kiểm tra người dùng có tồn tại hay không
    $sql = "SELECT user_id, user_name, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if($email === "admin@fpt.com") {
        header("Location: admin/admin.php");
        exit();
    }

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $user_id, $db_username, $hashed_password);
            mysqli_stmt_fetch($stmt);

            // Kiểm tra mật khẩu
            if (password_verify($password, $hashed_password)) {
                header("Location: Home.html");
                exit();
            }else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "User not found!";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login">
        <h1>Login</h1>
        
        <?php if (!empty($error_message)) : ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button class="btnlogin" type="submit"> Login </button>
            <br><br>
            <a href="register.php">Create new account</a>
        </form>
    </div>
</body>
</html>
