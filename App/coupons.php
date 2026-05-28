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
    <link rel="icon" href="src/zeg.png">
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
                echo '<h4 class="ms-auto font-weight-bold">Zalogowano jako: ' . htmlspecialchars($_SESSION['username']) . '<a href="profile.php" class="ms-2"><img src="src/user.png" alt="Profil" class="socialImg img-fluid"></a></h4>';
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
    <main class="flex-grow-1 d-flex align-items-center justify-content-center flex-column flex-fill">
        <?php
            if (!isset($_SESSION['logged_in'])) {
                echo '<div class="alert alert-danger mt-5" role="alert">Musisz być zalogowany, aby zobaczyć kupony.<br><sub><a href="signIn.php" id="LogInAccExistHref" class="m-2">Zarejestruj się</a></sub><sub><a href="logIn.php" id="LogInAccExistHref" class="m-2">Zaloguj się</a></sub></div>';
            } else {
                $query = "SELECT 
                            kupony.id,
                            kupony.nazwa,
                            kupony.cena,
                            GROUP_CONCAT(produkty.id) AS produkty_ids,
                            GROUP_CONCAT(produkty.zdjecie) AS zdjecia
                        FROM kupony
                        JOIN kupony_produkty ON kupony.id = kupony_produkty.id_kuponu
                        JOIN produkty ON kupony_produkty.id_produktu = produkty.id
                        GROUP BY kupony.id, kupony.nazwa, kupony.cena;";
                $coupons = mysqli_query($conn, $query);
                if (mysqli_num_rows($coupons) > 0) {    
                    echo '<div class="d-flex flex-wrap justify-content-center w-100">';
                    while($row = mysqli_fetch_array($coupons)) {
                        $images = explode(',', $row['zdjecia']);
                        echo '<div class="couponBox d-flex flex-column align-items-center m-3 p-3">';
                        echo "<div class='d-flex justify-content-center w-100'>";
                        foreach ($images as $image) {
                            echo "<img src='src/$image' alt='{$row['nazwa']}' class='w-25 m-2' style='max-height: 50px;'>";
                        }
                        echo '</div>';
                        echo "<h3>$row[nazwa]</h3>za jedyne $row[cena]zł!";
                        echo "<button 
                                    class='addToCartBtn'
                                    data-id='$row[id]'
                                    data-products='$row[produkty_ids]'
                                    data-name='$row[nazwa]'
                                    data-price='$row[cena]'
                                    data-image='$row[zdjecia]'
                                >
                                Dodaj do koszyka
                            </button>";
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info mt-5" role="alert">Brak dostępnych kuponów.</div>';
                }
            }
        ?>
    </main>
    <footer class="d-flex align-items-center justify-content-center p-2">
        <div class="me-auto">
            Zamów przez telefon <br>
            +48 123 456 789
        </div>
        <div class="position-absolute">
            <a href="new.php" class="m-2">Nowości</a>
            <a href="aboutUs.php" class="m-2">O nas</a>
            <a href="contact.php" class="m-2">Kontakt</a>
        </div>
        <a href="https://www.zs4.oswiata.tychy.pl/" class="ms-auto">Strona zegu</a>
    </footer>

    <div id="cartBox" class="d-none">
        <div id="cartSummary">
            <span id="cartItems">0</span> produktów |
            <span id="cartTotal">0.00</span> zł
        </div>

        <div id="cartExpanded" class="d-none">

            <div id="cartItemsList"></div>

            <a href='cart.php' class='btn btn-danger w-100 mt-2'>
                Przejdź do koszyka
            </a>

        </div>
    </div>

    <script>
        const cartBox = document.getElementById("cartBox");
        const cartSummary = document.getElementById("cartSummary");
        const cartExpanded = document.getElementById("cartExpanded");
        const cartItems = document.getElementById("cartItems");
        const cartTotal = document.getElementById("cartTotal");
        const cartItemsList = document.getElementById("cartItemsList");

        function getCart() {
            return JSON.parse(localStorage.getItem("cart")) || [];
        }

        function saveCart(cart) {
            localStorage.setItem("cart", JSON.stringify(cart));
        }

        function renderCart() {
            const cart = getCart();

            let totalItems = 0;
            let totalPrice = 0;

            cartItemsList.innerHTML = "";

            cart.forEach(item => {

                totalItems += item.quantity;
                totalPrice += item.price * item.quantity;

                const images = (item.image || "").split(",");

                let imagesHTML = "";

                images.forEach(img => {
                    imagesHTML += `
                        <img 
                            src="src/${img.trim()}" 
                            width="50"
                            class="m-1 rounded"
                        >
                    `;
                });

                cartItemsList.innerHTML += `
                    <div class="cartItem d-flex align-items-center mb-2">

                        <div class="d-flex flex-wrap me-2">
                            ${imagesHTML}
                        </div>

                        <div>
                            <div>${item.name}</div>
                            <div>${item.quantity} x ${item.price} zł</div>
                        </div>

                    </div>
                `;
            });

            cartItems.innerText = totalItems;
            cartTotal.innerText = totalPrice.toFixed(2);

            if (totalItems > 0) {
                cartBox.classList.remove("d-none");
            }
            else {
                cartBox.classList.add("d-none");
            }
        }

        function addToCart(id, name, price, image, products) {
            let cart = getCart();

            cart.push({
                id: parseInt(id),
                name: name,
                price: parseFloat(price),
                image: image,
                products: products ? products.split(",").map(Number) : [],
                quantity: 1
            });

            saveCart(cart);
            renderCart();
        }

        cartSummary.addEventListener("click", () => {
            cartExpanded.classList.toggle("d-none");
        });

        document.querySelectorAll(".addToCartBtn").forEach(btn => {

            btn.addEventListener("click", () => {

                addToCart(
                    btn.dataset.id,
                    btn.dataset.name,
                    btn.dataset.price,
                    btn.dataset.image,
                    btn.dataset.products
                );

            });

        });

        renderCart();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php
    mysqli_close($conn);
?>