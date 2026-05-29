<?php
$conn = mysqli_connect("localhost", "root", "", "szama");
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Płatność - Zegowska Szama</title>

    <link rel="icon" href="src/zeg.png">

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
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
    <main class="flex-grow-1 container py-5">
        <h1 class="mb-4 text-center">
            Finalizacja zamówienia
        </h1>

        <div class="row">
            <div class="col-lg-7">
                <div class="card p-4 shadow-sm mb-4">
                    <h3 class="mb-4">
                        Dane zamówienia
                    </h3>

                    <form id="paymentForm">

                        <div class="mb-3">
                            <label class="form-label">
                                Imię i nazwisko
                            </label>

                            <input 
                                name="name"
                                value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                                type="text"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Numer telefonu
                            </label>

                            <input 
                                name="phone"
                                type="tel"
                                class="form-control"
                                required
                                value="<?php echo htmlspecialchars($_SESSION['phone']); ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Adres email
                            </label>

                            <input 
                                name="email"
                                value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                                type="email"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                Metoda płatności
                            </label>

                            <select name="payment" class="form-select" required>
                                <option value="">
                                    Wybierz metodę płatności
                                </option>

                                <option>
                                    Karta płatnicza
                                </option>

                                <option>
                                    BLIK
                                </option>

                                <option>
                                    Gotówka przy odbiorze
                                </option>

                                <option>
                                    Karta przy odbiorze
                                </option>
                            </select>
                        </div>

                        <button 
                            type="submit"
                            class="btn btn-danger w-100"
                        >
                            Zapłać
                        </button>

                    </form>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="card p-4 shadow-sm">

                    <h3 class="mb-4">
                        Twoje zamówienie
                    </h3>

                    <div id="orderSummary"></div>

                    <hr>

                    <h4 id="finalTotal">
                        Suma: 0.00 zł
                    </h4>

                </div>

            </div>

        </div>

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

        <a href="https://www.zs4.oswiata.tychy.pl/" class="ms-auto">
            Strona zegu
        </a>

    </footer>

    <script>

    const orderSummary = document.getElementById("orderSummary");
    const finalTotal = document.getElementById("finalTotal");

    function getCart() {
        return JSON.parse(localStorage.getItem("cart")) || [];
    }

    function renderOrder() {

        const cart = getCart();

        if (cart.length === 0) {

            orderSummary.innerHTML = `
                <div class="alert alert-info">
                    Koszyk jest pusty
                </div>
            `;

            return;
        }

        let total = 0;

        cart.forEach(item => {

            total += item.price * item.quantity;

            const images = item.image.split(",");

            let imagesHTML = "";

            images.forEach(img => {

                imagesHTML += `
                    <img 
                        src="src/${img.trim()}"
                        width="60"
                        class="m-1 rounded"
                    >
                `;

            });

            orderSummary.innerHTML += `

                <div class="border rounded p-3 mb-3">

                    <div class="d-flex flex-wrap mb-2">
                        ${imagesHTML}
                    </div>

                    <h5>${item.name}</h5>

                    <div>
                        ${item.quantity} x ${item.price} zł
                    </div>

                    <div>
                        Razem: ${(item.quantity * item.price).toFixed(2)} zł
                    </div>

                </div>

            `;
        });

        finalTotal.innerText = `
            Suma: ${total.toFixed(2)} zł
        `;
    }

    renderOrder();

    document.getElementById("paymentForm").addEventListener("submit", async (e) => {
        e.preventDefault();

        const cart = JSON.parse(localStorage.getItem("cart")) || [];

        if (cart.length === 0) {
            alert("Koszyk jest pusty");
            return;
        }

        const formData = new FormData(e.target);

        formData.append("cart", JSON.stringify(cart));

        const res = await fetch("placeOrder.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            localStorage.removeItem("cart");
            alert("Zamówienie złożone!");
            window.location.href = "menu.php";
        } else {
            alert("Błąd zamówienia");
        }
    });

    </script>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>