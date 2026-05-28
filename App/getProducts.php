<?php
$conn = mysqli_connect("localhost", "root", "", "szama");

$cat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

if ($cat > 0) {
    $query = "SELECT id, nazwa FROM produkty WHERE typ = $cat";
} else {
    $query = "SELECT id, nazwa FROM produkty";
}

$res = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($res)) {
    echo "<div>
        <label>
            <input type='checkbox' onchange='toggleProduct({$row['id']}, \"{$row['nazwa']}\")'>
            {$row['nazwa']}
        </label>
    </div>";
}
?>