<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit();
}
require 'db.php';

$username = $_SESSION['username'];
$pelanggan = $conn->query("SELECT * FROM pelanggan WHERE username='$username'")->fetch_assoc();

$error_message = '';

$history = $conn->query("SELECT * FROM pembelian WHERE id_pelanggan='{$pelanggan['id_pelanggan']}'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pelanggan Page</title>
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
        }
        input[type="text"], input[type="number"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
    <script>
        function validateForm() {
            var nomerListrik = document.forms["buyForm"]["nomer_listrik"].value;
            if (isNaN(nomerListrik)) {
                document.getElementById('error-message').innerText = 'Nomer Listrik harus berupa angka.';
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $pelanggan['nama']; ?></h1>
        <form action="logout.php" method="get">
            <button type="submit" class="logout-button">Logout</button>
        </form>
        <h2>Beli token listrik</h2>
        <div id="error-message" class="error-message"><?php echo $error_message; ?></div>
        <form name="buyForm" method="post" onsubmit="return validateForm()">
            <input type="text" name="nomer_listrik" placeholder="Nomer Listrik" required><br>
            <input type="number" name="total_rupiah" placeholder="Total Rupiah" required><br>
            <button type="submit">Beli</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomer_listrik = $_POST['nomer_listrik'];
            $total_rupiah = $_POST['total_rupiah'];
        
            // Server-side validation for numeric input
            if (!is_numeric($nomer_listrik)) {
                $error_message = 'Nomer Listrik harus berupa angka.';
            } else {
                // Insert the new purchase record to get the last inserted ID
                $sql = "INSERT INTO pembelian (id_pelanggan, nomer_listrik, total_rupiah, token_pulsa, approved) VALUES ('{$pelanggan['id_pelanggan']}', '$nomer_listrik', '$total_rupiah', '', 0)";
                if ($conn->query($sql) === TRUE) {
                    $last_id = $conn->insert_id;

                    // Generate a 20-digit token
                    $token_pulsa = '';
                    for ($i = 0; $i < 5; $i++) {
                        $token_pulsa .= str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    }

                    // Update the purchase record with the generated token
                    $sql_update = "UPDATE pembelian SET token_pulsa='$token_pulsa' WHERE id_pembelian='$last_id'";
                    if ($conn->query($sql_update) === TRUE) {
                        echo "<script>alert('Terima kasih, transaksi berhasil');</script>";
                        echo "Pembelian berhasil, tunggu admin menyetujui agar bisa melihat token pulsa.";
                    } else {
                        echo "Error: " . $sql_update . "<br>" . $conn->error;
                    }
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
        ?>

        <h2>Riwayat Transaksi</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nomer Listrik</th>
                <th>Total Rupiah</th>
                <th>Token Pulsa</th>
                <th>Disetujui</th>
            </tr>
            <?php while ($row = $history->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_pembelian']; ?></td>
                    <td><?php echo $row['nomer_listrik']; ?></td>
                    <td><?php echo $row['total_rupiah']; ?></td>
                    <td><?php echo $row['approved'] ? $row['token_pulsa'] : 'Menunggu disetujui admin'; ?></td>
                    <td><?php echo $row['approved'] ? 'Disetujui' : 'Belum'; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
