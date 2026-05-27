<?php
file_put_contents("debug.txt", print_r($_POST, true));

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

session_start();
$conn = mysqli_connect("localhost", "root", "", "szama");

if (!$conn) {
    die(json_encode([
        "success" => false,
        "error" => mysqli_connect_error()
    ]));
}

header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$payment = $_POST['payment'] ?? '';
$cart = json_decode($_POST['cart'], true);

if (!$cart || count($cart) === 0) {
    echo json_encode(["success" => false, "error" => "empty_cart"]);
    exit;
}

$total = 0;

foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$date = date("Y-m-d H:i:s");
$status = 1;
$total = (float)$total;

$stmt = mysqli_prepare($conn, "
    INSERT INTO zamowienia (uzytkownik_id, data, status, metoda_platnosci, cena)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => mysqli_error($conn)
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

mysqli_stmt_bind_param(
    $stmt,
    "isssd",
    $userId,
    $date,
    $status,
    $payment,
    $total
);

mysqli_stmt_execute($stmt);

$orderId = mysqli_insert_id($conn);

mysqli_stmt_close($stmt);

$stmtItem = mysqli_prepare($conn, "
    INSERT INTO zamowienia_produkty (numer_zamowienia, produkt_id, ilosc)
    VALUES (?, ?, ?)
");

if (!$stmtItem) {
    echo json_encode([
        "success" => false,
        "error" => mysqli_error($conn)
    ]);
    exit;
}

foreach ($cart as $item) {

    $qty = (int)$item['quantity'];

    if (!empty($item['products'])) {

    if (is_string($item['products'])) {

        $products = array_filter(explode(",", $item['products']), function($v) {
            return $v !== "" && is_numeric($v);
        });

        } else {
            $products = $item['products'];
        }

        foreach ($products as $productId) {

            $productId = (int)$productId;

            if ($productId <= 0) {
                continue;
            }

            mysqli_stmt_bind_param($stmtItem, "iii", $orderId, $productId, $qty);
            mysqli_stmt_execute($stmtItem);
        }
    } else {

        $productId = (int)$item['id'];

        mysqli_stmt_bind_param($stmtItem, "iii", $orderId, $productId, $qty);
        mysqli_stmt_execute($stmtItem);
    }
}

mysqli_stmt_close($stmtItem);

echo json_encode(["success" => true]);