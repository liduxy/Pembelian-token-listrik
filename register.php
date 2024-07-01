<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $check = $conn->query("SELECT * FROM pelanggan WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        echo "Username atau email sudah ada";
    } else {
        // Insert new pelanggan
        $sql = "INSERT INTO pelanggan (username, nama, email, password) VALUES ('$username', '$nama', '$email', '$password')";
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
    <title>Register Pelanggan</title>
</head>
<body>
    <h1>Register</h1>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Nama: <input type="text" name="nama" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
