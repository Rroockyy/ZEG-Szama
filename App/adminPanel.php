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
    <nav class="adminNav">
        <div class="navItem">
            Produkty
            <div class="dropdown">
                <div class="createProductNav">Stwórz nowy produkt</div>
                <div class="deleteProductNav">Usuń produkt</div>
                <div class="editProductNav">Edytuj produkt</div>
            </div>
        </div>

        <div class="navItem">
            Kupony
            <div class="dropdown">
                <div class="createCouponNav">Stwórz nowy kupon</div>
                <div class="deleteCouponNav">Usuń kupon</div>
                <div class="editCouponNav">Edytuj kupon</div>
            </div>
        </div>

        <div class="navItem">
            Zarządzanie
            <div class="dropdown">
                <div class="manageOrdersNav">Zarządzaj zamówieniami</div>
                <div class="manageUsersNav">Zarządzaj użytkownikami</div>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1 d-flex align-items-center flex-column flex-fill adminPanelMain">
        <div id="createProduct" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
            <h2 class="mb-4">Stwórz nowy produkt</h2>
            <form action="#" method="POST" enctype="multipart/form-data" class="d-flex flex-column align-items-center">
                <input type="text" name="productName" placeholder="Nazwa produktu" class="mb-3 form-control w-75">
                <input type="number" inputmode="decimal" name="productPrice" step="0.01" placeholder="Cena produktu" class="mb-3 form-control w-75">
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
                <button type="submit" class="btn btn-primary" id="createProductBtn" name="createProductBtn">Stwórz produkt</button>
                <?php
if (isset($_POST['createProductBtn'])) {
    $name = $_POST['productName'] ?? '';
    $price = $_POST['productPrice'] ?? '';
    $type = $_POST['productType'] ?? ''; 
    $image = $_FILES['productImage'] ?? null;

    if ($name && $price && $type && $image) {

        $prefixes = [
            1 => 'b', 
            2 => 'h',
            3 => 't', 
            4 => 'n' 
        ];

        $prefix = $prefixes[$type] ?? 'p';

        $nextId = 1; 
        $statusQuery = "SHOW TABLE STATUS LIKE 'produkty'";
        if ($statusResult = mysqli_query($conn, $statusQuery)) {
            $row = mysqli_fetch_assoc($statusResult);
            $nextId = $row['Auto_increment'];
        }

        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $imageName = $prefix . $nextId . '.' . $extension;

        $imagePath = 'src/' . $imageName;
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $query = "INSERT INTO produkty (nazwa, cena, typ, zdjecie) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $query)) {
                
                mysqli_stmt_bind_param($stmt, 'sdis', $name, $price, $type, $imageName);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                echo '<div class="alert alert-success mt-3">Produkt został stworzony.</div>';
            } else {
                echo '<div class="alert alert-danger mt-3">Błąd bazy danych.</div>';
            }
        } else {
            echo '<div class="alert alert-danger mt-3">Błąd podczas przesyłania pliku.</div>';
        }
    } else {
        echo '<div class="alert alert-danger mt-3">Wszystkie pola są wymagane.</div>';
    }
}
?>
            </form>

        </div>
        <div id="deleteProduct" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
            <h2 class="mb-4">Usuń produkt</h2>
            <form method="GET" action="#" class="d-flex flex-column align-items-center w-100">
                <select name="categoryFilter" class="mb-3 form-control w-100" onchange="this.form.submit()">
                    <option value="">-- Wybierz kategorię --</option>
                    <?php
                        $query = "SELECT id, typ FROM typy_produktow";
                        $types = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($types)) {
                            $selected = isset($_GET['categoryFilter']) && $_GET['categoryFilter'] == $row['id'] ? 'selected' : '';
                            echo "<option value='$row[id]' $selected>$row[typ]</option>";
                        }
                    ?>
                </select>
            </form>    
<?php
// musialem przesunac usuwanie na gorze zeby produkt fajnie znikal a nie ze usuwasz i cie do indii daje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteProduct'])) {
    $productId = intval($_POST['productId']);
    // usuwa zdjęcie produktu z serwera
    $imgQuery = "SELECT zdjecie FROM produkty WHERE id = $productId";
    $imgResult = mysqli_query($conn, $imgQuery);
    if ($imgRow = mysqli_fetch_assoc($imgResult)) {
        $sciezkaDoPliku = 'src/' . $imgRow['zdjecie'];
        if (file_exists($sciezkaDoPliku) && !empty($imgRow['zdjecie'])) {
            unlink($sciezkaDoPliku); 
        }
    }

    $deleteQuery = "DELETE FROM produkty WHERE id = $productId";
    mysqli_query($conn, $deleteQuery);

    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['categoryFilter']) ? "?categoryFilter=" . $_GET['categoryFilter'] : ""));
    exit();
}
if (isset($_GET['categoryFilter']) && $_GET['categoryFilter'] != '') {
    $categoryId = intval($_GET['categoryFilter']);
    $query2 = "SELECT id, zdjecie, nazwa, cena FROM produkty WHERE typ = $categoryId ORDER BY id ASC";
    $products = mysqli_query($conn, $query2);
    
    echo "<div class='d-flex flex-row flex-wrap justify-content-center gap-3 w-100'>";
    while($row2 = mysqli_fetch_array($products)) {
        echo "<div class='productBox'>";
        echo "<img src='src/{$row2['zdjecie']}' alt='{$row2['nazwa']}' class='w-50'>";
        echo "<span>{$row2['nazwa']}</span>";
        echo "<span>{$row2['cena']} zł</span>";
        
        echo "<form action='' method='POST' style='margin-top: 10px;' onsubmit='return confirm(\"Na pewno chcesz usunąć ten produkt?\")'>";
        echo "<input type='hidden' name='productId' value='{$row2['id']}'>";
        echo "<button type='submit' name='deleteProduct' class='btn btn-danger btn-sm'>Usuń</button>";
        echo "</form>";
        echo "</div>";
    }
    echo "</div>";
}
?>
        </div>
        <div id="createCoupon" class="adminPanelSection d-flex flex-column align-items-center card p-4 shadow bg-light rounded">
        <h2 class="mb-4">Stwórz nowy kupon</h2>

        <form method="POST" class="d-flex flex-column align-items-center w-100">
            <input type="text" name="couponCode" placeholder="nazwa" class="mb-3 form-control w-75" required>
            <input type="number" name="couponDiscount" placeholder="cena" class="mb-3 form-control w-75" required>

            <button type="button" id="openProductPicker" class="btn btn-secondary mb-3">
                Dodaj produkty
            </button>

            <div id="selectedProductsPreview" class="mb-3"></div>

            <input type="hidden" name="selectedProducts" id="selectedProducts">

            <button type="submit" name="createCouponBtn" class="btn btn-primary">
                Stwórz kupon
            </button>
            <?php
                if (isset($_POST['createCouponBtn'])) {

                $code = $_POST['couponCode'];
                $discount = $_POST['couponDiscount'];
                $products = $_POST['selectedProducts']; // "1,2,3"

                if ($code && $discount && $products) {

                    $stmt = mysqli_prepare($conn, "INSERT INTO kupony (nazwa, cena) VALUES (?, ?)");
                    mysqli_stmt_bind_param($stmt, "si", $code, $discount);
                    mysqli_stmt_execute($stmt);

                    $couponId = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt);

                    $productArray = explode(',', $products);

                    $stmt2 = mysqli_prepare($conn, "INSERT INTO kupony_produkty (id_kuponu, id_produktu) VALUES (?, ?)");

                    foreach ($productArray as $productId) {
                        $pid = intval($productId);
                        mysqli_stmt_bind_param($stmt2, "ii", $couponId, $pid);
                        mysqli_stmt_execute($stmt2);
                    }

                    mysqli_stmt_close($stmt2);

                    echo "<div class='alert alert-success'>Kupon dodany</div>";

                } else {
                    echo "<div class='alert alert-danger'>Wybierz min. 1 produkt</div>";
                }
            }
            ?>
        </form>
    </div>

    <div id="productModal" class="modalCustom d-none">
        <div class="modalContent card p-3">
            <h4>Wybierz produkty</h4>

            <select id="couponCategoryFilter" class="form-control mb-2">
                <option value="">-- kategoria --</option>
                <?php
                    $query = "SELECT id, typ FROM typy_produktow";
                    $types = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_array($types)) {
                        echo "<option value='{$row['id']}'>{$row['typ']}</option>";
                    }
                ?>
            </select>

            <div id="productList" class="mb-2" style="max-height:300px; overflow:auto;"></div>

            <button type="button" id="confirmProducts" class="btn btn-success">
                Dodaj wybrane
            </button>

            <button type="button" id="closeModal" class="btn btn-danger mt-2">
                Zamknij
            </button>
        </div>
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
            showTab('createProduct');
            createProductSection.classList.remove('d-none');
            deleteProductSection.classList.add('d-none');
            createCouponSection.classList.add('d-none');
        });

        deleteProductNav.addEventListener('click', () => {
            showTab('deleteProduct');
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.remove('d-none');
            createCouponSection.classList.add('d-none');
        });

        createCouponNav.addEventListener('click', () => {
            showTab('createCoupon');
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.add('d-none');
            createCouponSection.classList.remove('d-none');
        });
        //to ci pobiera jakis parametr url i na jego podstawie wchodzi ci w odpowiednia zakladke bo bez tego to cie wywala do kuponow 
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('categoryFilter') && urlParams.get('categoryFilter') !== '') {
            deleteProductSection.classList.remove('d-none');
            createProductSection.classList.add('d-none');
            createCouponSection.classList.add('d-none');
        }

        function showTab(tab) {
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.add('d-none');
            createCouponSection.classList.add('d-none');

            if (tab === 'createProduct') createProductSection.classList.remove('d-none');
            if (tab === 'deleteProduct') deleteProductSection.classList.remove('d-none');
            if (tab === 'createCoupon') createCouponSection.classList.remove('d-none');

            localStorage.setItem('activeTab', tab);
        }


        const modal = document.getElementById('productModal');
        const openBtn = document.getElementById('openProductPicker');
        const closeBtn = document.getElementById('closeModal');
        const categoryFilter = document.getElementById('couponCategoryFilter');
        const productList = document.getElementById('productList');
        const selectedProductsInput = document.getElementById('selectedProducts');
        const preview = document.getElementById('selectedProductsPreview');

        let selected = [];

        openBtn.addEventListener('click', () => {
            modal.classList.remove('d-none');
            loadProducts();
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.add('d-none');
        });

        categoryFilter.addEventListener('change', loadProducts);

        function loadProducts() {
            const cat = categoryFilter.value;

            fetch(`getProducts.php?cat=${cat}`)
                .then(res => res.text())
                .then(html => {
                    productList.innerHTML = html;
                });
        }

        function toggleProduct(id, name) {
            if (selected.includes(id)) {
                selected = selected.filter(x => x !== id);
            } else {
                selected.push(id);
            }

            updateSelected();
        }

        function updateSelected() {
            selectedProductsInput.value = selected.join(',');

            preview.innerHTML = selected.map(id =>
                `<span class="badge bg-primary m-1">ID: ${id}</span>`
            ).join('');
        }

        document.getElementById('confirmProducts').addEventListener('click', () => {
            modal.classList.add('d-none');
        });

        const savedTab = localStorage.getItem('activeTab');

        if (savedTab) {
            showTab(savedTab);
        }
    </script>
</body>
</html>

<?php
    mysqli_close($conn);
?>