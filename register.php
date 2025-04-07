<?php
// Include Database
require 'connect.php';

// Khởi tạo biến thông báo
$message = "";

// Kiểm tra nếu form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["fullname"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phonenumber"]);
    $email = trim($_POST["email"]);

    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Câu lệnh SQL để chèn dữ liệu
    $sql = "INSERT INTO users (user_name, password, phone, email) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $full_name, $hashed_password, $phone, $email);
        if (mysqli_stmt_execute($stmt)) {
            $message = "";
            header("location:login.php"); // Chuyển hướng sau 2 giây
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing statement: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<div class="container">
    <h1>Sign up for an account</h1>

    <?php if (!empty($message)) : ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label for="fullname">Full Name*</label>
        <input type="text" id="fullname" name="fullname" required>  

        <label for="phonenumber">Phone Number *</label>
        <input type="tel" id="phonenumber" name="phonenumber" required>

        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required>

        <div class="password-table">
            <div>
                <label for="password">Pass Word *</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>

        <button type="submit" class="submit-btn">Register</button>
        <p>Already have an account? <a href="../login/login.html"> Login </a></p>
    </form>
</div>
</body>
</html>
