<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists in admin table
    $check = $conn->query("SELECT * FROM admin WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        echo "Username atau email sudah ada";
    } else {
        // Insert new admin
        $sql = "INSERT INTO admin (username, nama, email, password) VALUES ('$username', '$nama', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "Registrasi berhasil. Silahkan <a href='login.php'>login</a>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
</head>
<body>
    <h1>Register Admin</h1>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Nama: <input type="text" name="nama" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
    <p><a href="register.php">Register pelanggan</a></p>
</body>
</html>
