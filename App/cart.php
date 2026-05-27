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
    <main class="flex-grow-1 d-flex align-items-center justify-content-center flex-column flex-fill">
        <h1 class="mt-5">Twój koszyk</h1>
        <div id="cartPage"></div>
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

    <script>
        const cartPage = document.getElementById("cartPage");

        function getCart() {
            return JSON.parse(localStorage.getItem("cart")) || [];
        }

        const isLoggedIn = <?php echo !empty($_SESSION['logged_in']) ? 'true' : 'false'; ?>;

        function renderCartPage() {

            const cart = getCart();

            if (cart.length === 0) {
                cartPage.innerHTML = `
                    <div class="alert alert-info">
                        Koszyk jest pusty
                    </div>
                `;
                return;
            }

            let total = 0;

            cartPage.innerHTML = "";

            cart.forEach((item, index) => {

                total += item.price * item.quantity;

                const images = item.image.split(",");

                let imagesHTML = "";

                images.forEach(img => {

                    imagesHTML += `
                        <img 
                            src="src/${img.trim()}" 
                            width="80"
                            class="m-1 rounded"
                        >
                    `;

                });

                cartPage.innerHTML += `

                    <div class="cartProduct border rounded p-3 m-3">

                        <div class="d-flex flex-wrap mb-2">
                            ${imagesHTML}
                        </div>

                        <h3>${item.name}</h3>

                        <p>
                            ${item.quantity} x ${item.price} zł
                        </p>

                        <p>
                            Razem: ${(item.price * item.quantity).toFixed(2)} zł
                        </p>

                        <button 
                            class="btn btn-danger"
                            onclick="removeItem(${index})"
                        >
                            Usuń
                        </button>

                    </div>

                `;
            });

            cartPage.innerHTML += `
                <div id="cartTotal" class="m-3 p-3 border rounded d-flex flex-row justify-content-center align-items-center gap-5">
                    <h2>
                        Suma: ${total.toFixed(2)} zł
                    </h2>

                    ${
                        isLoggedIn
                        ? `<a href="payment.php" class="btn btn-danger">Zamów i zapłać</a>`
                        : `<a href="logIn.php" class="btn btn-danger">Zaloguj się aby zamówić</a>`
                    }
                </div>
            `;
        }

        function removeItem(index) {

            let cart = getCart();

            cart.splice(index, 1);

            localStorage.setItem("cart", JSON.stringify(cart));

            renderCartPage();
        }

        renderCartPage();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php
    mysqli_close($conn);
?>