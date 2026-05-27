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
    <nav class="d-flex flex-row align-items-center w-100">
        <div class="createProductNav">Stwórz nowy produkt</div>
        <div class="deleteProductNav">usuń produkt</div>
        <div class="createCouponNav">Stwórz nowy kupon</div>
    </nav>
    <main class="flex-grow-1 d-flex align-items-center flex-column flex-fill adminPanelMain">
        <div id="createProduct" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
            <h2 class="mb-4">Stwórz nowy produkt</h2>
            <form action="createProduct.php" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-center">
                <input type="text" name="productName" placeholder="Nazwa produktu" class="mb-3 form-control w-75">
                <input type="number" inputmode="decimal" name="productPrice" placeholder="Cena produktu" class="mb-3 form-control w-75">
                <label for="productType" class="mb-2">Wybierz typ produktu:</label>
                <select name="productType" class="mb-3 form-control w-75">
                    <?php
                        $query = "SELECT id, typ FROM typy_produktow";
                        $types = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($types)) {
                            echo "<option value='$row[id]'>$row[typ]</option>";
                        }
                    ?>
                </select>
                <label for="productImage" class="mb-2">Wybierz zdjęcie produktu:</label>
                <input type="file" name="productImage" accept=".jpg, .jpeg, .png" class="mb-3 form-control w-75">
                <button type="submit" class="btn btn-primary">Stwórz produkt</button>
            </form>

        </div>
        <div id="deleteProduct" class="adminPanelSection d-flex flex-column align-items-center d-none">
            <h2 class="mb-4">Usuń produkt</h2>
            <form action="deleteProduct.php" method="POST" class="d-flex flex-column align-items-center">
                <select name="productId" class="mb-3 form-control w-50">
                    <?php
                        $query = "SELECT id, nazwa FROM produkty";
                        $products = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($products)) {
                            echo "<option value='$row[id]'>$row[nazwa]</option>";
                        }
                    ?>
                </select>
                <button type="submit" class="btn btn-danger">Usuń produkt</button>
            </form>
        </div>
        <div id="createCoupon" class="adminPanelSection d-flex flex-column align-items-center">
            <h2 class="mb-4">Stwórz nowy kupon</h2>
            <form action="createCoupon.php" method="POST" class="d-flex flex-column align-items-center">
                <input type="text" name="couponCode" placeholder="Kod kuponu" class="mb-3 form-control w-50">
                <input type="number" name="couponDiscount" placeholder="Zniżka (w procentach)" class="mb-3 form-control w-50">
                <button type="submit" class="btn btn-primary">Stwórz kupon</button>
            </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        const createProductNav = document.querySelector('.createProductNav');
        const deleteProductNav = document.querySelector('.deleteProductNav');
        const createCouponNav = document.querySelector('.createCouponNav');
        const createProductSection = document.getElementById('createProduct');
        const deleteProductSection = document.getElementById('deleteProduct');
        const createCouponSection = document.getElementById('createCoupon');

        createProductNav.addEventListener('click', () => {
            createProductSection.classList.remove('d-none');
            deleteProductSection.classList.add('d-none');
            createCouponSection.classList.add('d-none');
        });

        deleteProductNav.addEventListener('click', () => {
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.remove('d-none');
            createCouponSection.classList.add('d-none');
        });

        createCouponNav.addEventListener('click', () => {
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.add('d-none');
            createCouponSection.classList.remove('d-none');
        });
    </script>
</body>
</html>

<?php
    mysqli_close($conn);
?>