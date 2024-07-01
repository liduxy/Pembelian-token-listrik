<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'admin') {
        $sql = "SELECT * FROM admin WHERE username='$username'";
    } else {
        $sql = "SELECT * FROM pelanggan WHERE username='$username'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            if ($role == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: pelanggan.php");
            }
        } else {
            echo "<script>alert('Username atau Password salah');</script>";
        }
    } else {
        echo "<script>alert('Username atau Password salah');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="pelanggan">Pelanggan</option>
            </select><br>
            <button type="submit">Login</button>
        </form>
        <p>Belum punya akun? <a href="register.php">Register</a></p>
    </div>
</body>
</html>
