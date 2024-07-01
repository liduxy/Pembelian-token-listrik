<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require 'db.php';

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE pembelian SET approved=1 WHERE id_pembelian='$id'");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pembelian WHERE id_pembelian='$id'");
}

// Join the pembelian and pelanggan tables to get the necessary information
$purchases = $conn->query("SELECT pembelian.*, pelanggan.username, pelanggan.nama FROM pembelian JOIN pelanggan ON pembelian.id_pelanggan = pelanggan.id_pelanggan");

$username = $_SESSION['username'];
$adminQuery = $conn->query("SELECT nama FROM admin WHERE username='$username'");
$admin = $adminQuery->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <!-- <link rel="stylesheet" type="text/css" href="styles.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 900px;
            text-align: center;
            margin: 20px;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .logout-button {
            background-color: #dc3545;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $admin['nama']; ?></h1>
        <form action="logout.php" method="get">
            <button type="submit" class="logout">Logout</button>
        </form>
        <!-- <form action="register-admin.php" method="get">
            <button type="submit">Register Admin</button>
        </form> -->

        <h2>Transaksi Persetujuan</h2>
        <table border=1>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Nomer Listrik</th>
                <th>Total Rupiah</th>
                <th>Token Pulsa</th>
                <th>Disetujui</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $purchases->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_pembelian']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['nomer_listrik']; ?></td>
                    <td><?php echo $row['total_rupiah']; ?></td>
                    <td><?php echo $row['token_pulsa']; ?></td>
                    <td><?php echo $row['approved'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <?php if (!$row['approved']) { ?>
                            <a href="admin.php?approve=<?php echo $row['id_pembelian']; ?>"><button>Setujui</button></a>
                        <?php } ?>
                        <a href="admin.php?delete=<?php echo $row['id_pembelian']; ?>" onclick="return confirm('Apakah kamu ingin menhapus transaksi ini?');"><button class="logout-button">Delete</button></a>
                        
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
