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
    <nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm"> 
        <a href="menu.php" class="navbar-brand"><img src="src/zegowska_szama-logo.png" id="logo" alt="logo" class="img-fluid"></a>

        <button class="navbar-toggler bg-white m-2" data-bs-toggle="collapse" data-bs-target="#navbarNav" >
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center w-100">
                <div class="d-lg-flex order-2 order-lg-1">
                    <a href="menu.php" class="m-3 nav-item nav-link">Menu</a>
                    <a href="coupons.php" class="m-3 nav-item nav-link">Kupony</a>
                </div>
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
                    echo '<div class="ms-lg-auto text-lg-end text-start order-1 order-lg-2 m-3">';
                        echo '<h4 class="ms-auto font-weight-bold">Zalogowano jako: ' . htmlspecialchars($_SESSION['username']) . '<a href="profile.php" class="ms-2 nav-item nav-link"><img src="src/user.png" alt="Profil" class="socialImg img-fluid"></a></h4>';
                        echo "<div class='d-flex justify-content-start justify-content-md-end gap-2'>";
                            if (intval($userDostep) === 2) {
                                echo '<a href="adminPanel.php" class="nav-item nav-link" id="adminPanel">Panel administratora</a>';
                            }
                            echo '<a href="logout.php" class="nav-item nav-link" id="logOut">Wyloguj się</a>';
                        echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="ms-lg-auto text-lg-end text-start order-1 order-lg-2 m-3">';
                        echo "<div class='d-flex justify-content-start flex-column flex-md-row justify-content-md-end gap-2'>";
                            echo '<a href="logIn.php" class="nav-item nav-link">Logowanie</a>';
                            echo '<a href="signIn.php" class="nav-item nav-link" id="SignInBtn">Rejestracja</a>';
                        echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1 d-flex align-items-center flex-column flex-fill">
        <h1 class="mt-5">Kontakt</h1>
        <p class="mt-3 text-center w-75 fs-3">
            Masz pytania? Skontaktuj się z nami!
        </p>
        <div class="d-flex flex-column align-items-center mt-4">
            <div class="d-flex align-items-center mb-3">
                <img src="src/phone.png" alt="Telefon" class="socialImg">
                <span class="fs-4">+48 123 456 789</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <img src="src/email.png" alt="Email" class="socialImg">
                <span class="fs-4">kontakt@zegowska-szama.pl</span>
            </div>
        </div>
    </main>
    <footer class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between p-3 gap-2">
        <div class="text-center text-md-start">
            Zamów przez telefon <br>
            +48 123 456 789
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="new.php">Nowości</a>
            <a href="aboutUs.php">O nas</a>
            <a href="contact.php">Kontakt</a>
        </div>

        <a href="https://www.zs4.oswiata.tychy.pl/" class="text-center text-md-end">
            Strona szkoły
        </a>
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

                const images = item.image.split(",");

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

        function addToCart(id, name, price, image) {

            let cart = getCart();

            const existing = cart.find(item => item.name === name);

            if (existing) {
                existing.quantity++;
            }
            else {
                cart.push({
                    id: id,
                    name: name,
                    price: parseFloat(price),
                    image: image,
                    quantity: 1
                });
            }

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
                    btn.dataset.image
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