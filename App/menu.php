<?php
$conn = mysqli_connect("localhost", "root", "", "szama");
session_start();
?>



<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zegowska Szama</title>
    <link rel="icon" href="zeg.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="min-vh-100 d-flex flex-column">
    <header class="sticky-top d-flex align-items-center justify-content-center p-2"> 
        <a href="menu.php" class="m-3">Menu</a>
        <a href="coupons.php" class="me-auto">Kupony</a>
        <a href="menu.php"><img src="src/zegowska_szama-logo.png" alt="logo" class=""></a>
        <?php
        if (!empty($_SESSION['logged_in']) && !empty($_SESSION['username'])) {
            $userDostep = 0;
            if (!empty($_SESSION['user_id']) && $stmt = mysqli_prepare($conn, "SELECT dostep FROM uzytkownicy WHERE id = ?")) {
                $userId = intval($_SESSION['user_id']);
                mysqli_stmt_bind_param($stmt, 'i', $userId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $userDostep);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
                if (!isset($_SESSION['dostep'])) {
                    $_SESSION['dostep'] = $userDostep;
                }
            }
            echo '<div class="ms-auto text-end">';
                echo '<h4 class="ms-auto font-weight-bold">Zalogowano jako: ' . htmlspecialchars($_SESSION['username']) . '</h4>';
                if (intval($userDostep) === 2) {
                    echo '<a href="adminPanel.php" class="me-3" id="adminPanel">Panel administratora</a>';
                }
                echo '<a href="logout.php" class="ms-3" id="logOut">Wyloguj się</a>';
            echo '</div>';
        } else {
            echo '<a href="logIn.php" class="ms-auto">Logowanie</a>';
            echo '<a href="signIn.php" class="m-3" id="SignInBtn">Rejestracja</a>';
        }
        ?>
    </header>
    <main class="flex-grow-1 d-flex align-items-center flex-column">
        <div class="d-flex justify-content-around w-100 mt-3">
            <?php
                $query = "SELECT typy_produktow.typ, produkty.zdjecie FROM typy_produktow JOIN produkty ON typy_produktow.id = produkty.typ WHERE produkty.zdjecie LIKE '_1.jpg' ORDER BY typy_produktow.id ASC";
                $types = mysqli_query($conn, $query);
                while($row = mysqli_fetch_array($types)) {
                    echo "<a href='#type-$row[typ]'><div class='itemsType d-flex flex-column align-items-center'>";
                    echo "<img src='src/$row[zdjecie]' alt='$row[typ]' class='w-100'>";
                    echo "$row[typ]";
                    echo "</div></a>";
                }
            ?>
        </div>
        <div>
            <?php
                $query = "SELECT id, typ FROM typy_produktow ORDER BY id ASC";
                $pages = mysqli_query($conn, $query);
                while($row = mysqli_fetch_array($pages)) {
                    echo "<hr class='vw-100'>";
                    echo "<div id='type-$row[typ]' class='m-4'>";
                    echo "<h2>$row[typ]</h2>";

                        $query2 = "SELECT zdjecie, nazwa, cena FROM produkty WHERE typ = $row[id] ORDER BY id ASC";
                        $products = mysqli_query($conn, $query2);
                        while($row2 = mysqli_fetch_array($products)) {
                            echo "<div class='productBox";
                            if(($row2['zdjecie'])[1] == "1") {
                                echo " bestSeller'>";
                                echo "<h3>Best Seller!</h3>";
                            }
                            else {
                                echo "'>";
                            }
                            echo "<img src='src/$row2[zdjecie]' alt='$row2[nazwa]' class='w-50'>";
                            echo "<span>$row2[nazwa]</span>";
                            echo "<span>$row2[cena]zł</span>";
                            echo "<button 
                                    class='addToCartBtn'
                                    data-name='$row2[nazwa]'
                                    data-price='$row2[cena]'
                                    data-image='$row2[zdjecie]'
                                    >
                                    Dodaj do koszyka
                                </button>";
                            echo "</div>";
                        }

                    echo "</div>";
                }
            ?>
        </div>
    </main>
    <footer class="d-flex align-items-center justify-content-center p-2">
        <div class="me-auto">
            Zamów przez telefon <br>
            +48 123 456 789
        </div>
        <div class="position-absolute">
            <a href="" class="m-2">Nowości</a>
            <a href="aboutUs.php" class="m-2">O nas</a>
            <a href="" class="m-2">Kontakt</a>
        </div>
        <a href="https://www.zs4.oswiata.tychy.pl/" class="ms-auto">Strona zegu</a>
    </footer>

    <?php
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $totalItems = 0;
    $totalPrice = 0;

    foreach ($_SESSION['cart'] as $item) {
        $totalItems += $item['quantity'];
        $totalPrice += $item['price'] * $item['quantity'];
    }
    ?>

    <div id="cartBox" class="<?php echo $totalItems > 0 ? '' : 'd-none'; ?>">
        
        <div id="cartSummary">
            <span id="cartItems"><?php echo $totalItems; ?></span> produktów |
            <span id="cartTotal"><?php echo number_format($totalPrice, 2); ?></span> zł
        </div>

        <div id="cartExpanded" class="d-none">
            
            <div id="cartItemsList">
                <?php
                foreach ($_SESSION['cart'] as $item) {
                    echo "
                    <div class='cartItem'>
                        <img src='src/{$item['image']}' width='50'>
                        <div>
                            <div>{$item['name']}</div>
                            <div>{$item['quantity']} x {$item['price']} zł</div>
                        </div>
                    </div>
                    ";
                }
                ?>
            </div>

            <a href='cart.php' class='btn btn-danger w-100 mt-2'>
                Przejdź do koszyka
            </a>

        </div>
    </div>

    <script>
    const cartBox = document.getElementById("cartBox");
    const cartSummary = document.getElementById("cartSummary");
    const cartExpanded = document.getElementById("cartExpanded");

    cartSummary.addEventListener("click", () => {
        cartExpanded.classList.toggle("d-none");
    });

    document.querySelectorAll(".addToCartBtn").forEach(btn => {

        btn.addEventListener("click", () => {
            const formData = new FormData();

            formData.append("name", btn.dataset.name);
            formData.append("price", btn.dataset.price);
            formData.append("image", btn.dataset.image);

            fetch("cart.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                cartBox.classList.remove("d-none");

                document.getElementById("cartItems").innerText = data.items;
                document.getElementById("cartTotal").innerText = data.total;

                document.getElementById("cartItemsList").innerHTML = data.html;
            });

        });

    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php
    mysqli_close($conn);
?>