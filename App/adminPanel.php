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
    <nav class="adminNav">
        <div class="navItem btn btn-danger">
            Produkty
            <div class="dropdown">
                <div class="createProductNav btn btn-danger">Stwórz nowy produkt</div>
                <div class="deleteProductNav btn btn-danger">Usuń produkt</div>
                <div class="editProductNav btn btn-danger">Edytuj produkt</div>
            </div>
        </div>

        <div class="navItem btn btn-danger">
            Kupony
            <div class="dropdown">
                <div class="createCouponNav btn btn-danger">Stwórz nowy kupon</div>
                <div class="deleteCouponNav btn btn-danger">Usuń kupon</div>
                <div class="editCouponNav btn btn-danger">Edytuj kupon</div>
            </div>
        </div>

        <div class="navItem btn btn-danger">
            Zarządzanie
            <div class="dropdown">
                <div class="manageOrdersNav btn btn-danger">Zarządzaj zamówieniami</div>
                <div class="manageUsersNav btn btn-danger">Zarządzaj użytkownikami</div>
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

        <div id="editProduct" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
            <h2 class="mb-4">Modyfikuj produkt</h2>
            
            <form method="GET" action="#" class="d-flex flex-column align-items-center w-100 mb-4">
                <select name="editCategoryFilter" class="mb-3 form-control w-75" onchange="this.form.submit()">
                    <option value="">-- Wybierz kategorię --</option>
                    <?php
                        $query = "SELECT id, typ FROM typy_produktow";
                        $types = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($types)) {
                            $selected = isset($_GET['editCategoryFilter']) && $_GET['editCategoryFilter'] == $row['id'] ? 'selected' : '';
                            echo "<option value='$row[id]' $selected>$row[typ]</option>";
                        }
                    ?>
                </select>
            </form>

            <?php
            if (isset($_POST['updateProductBtn'])) {
                $pId = intval($_POST['editProductId']);
                $pName = $_POST['editProductName'] ?? '';
                $pPrice = $_POST['editProductPrice'] ?? '';
                $pType = $_POST['editProductType'] ?? '';
                $pImage = $_FILES['editProductImage'] ?? null;

                if ($pId && $pName && $pPrice && $pType) {
                    if ($pImage && $pImage['error'] == 0) {
                        $prefixes = [1 => 'b', 2 => 'h', 3 => 't', 4 => 'n'];
                        $prefix = $prefixes[$pType] ?? 'p';
                        $extension = pathinfo($pImage['name'], PATHINFO_EXTENSION);
                        $imageName = $prefix . $pId . '.' . $extension;
                        $imagePath = 'src/' . $imageName;

                        if (move_uploaded_file($pImage['tmp_name'], $imagePath)) {
                            $updateQuery = "UPDATE produkty SET nazwa = ?, cena = ?, typ = ?, zdjecie = ? WHERE id = ?";
                            $stmt = mysqli_prepare($conn, $updateQuery);
                            mysqli_stmt_bind_param($stmt, 'sdisi', $pName, $pPrice, $pType, $imageName, $pId);
                        }
                    } else {
                        $updateQuery = "UPDATE produkty SET nazwa = ?, cena = ?, typ = ? WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $updateQuery);
                        mysqli_stmt_bind_param($stmt, 'sdii', $pName, $pPrice, $pType, $pId);
                    }

                    if (isset($stmt) && mysqli_stmt_execute($stmt)) {
                        echo '<div class="alert alert-success mt-2">Produkt zaktualizowany.</div>';
                        mysqli_stmt_close($stmt);
                    } else {
                        echo '<div class="alert alert-danger mt-2">Błąd zapisu zmian.</div>';
                    }
                }
            }

            if (isset($_GET['editCategoryFilter']) && $_GET['editCategoryFilter'] != '') {
                $editCatId = intval($_GET['editCategoryFilter']);
                $queryEdit = "SELECT id, zdjecie, nazwa, cena, typ FROM produkty WHERE typ = $editCatId ORDER BY id ASC";
                $productsEdit = mysqli_query($conn, $queryEdit);
                
                echo "<div class='d-flex flex-row flex-wrap justify-content-center gap-3 w-100'>";
                while($pRow = mysqli_fetch_array($productsEdit)) {
                    echo "<div class='productBox card p-2 d-flex flex-column align-items-center' style='width: 200px;'>";
                    echo "<img src='src/{$pRow['zdjecie']}' alt='{$pRow['nazwa']}' class='w-50 mb-2'>";
                    
                    echo "<form action='#' method='POST' enctype='multipart/form-data' class='w-100 d-flex flex-column'>";
                    echo "<input type='hidden' name='editProductId' value='{$pRow['id']}'>";
                    
                    echo "<input type='text' name='editProductName' value='".htmlspecialchars($pRow['nazwa'])."' class='form-control form-control-sm mb-1'>";
                    echo "<input type='number' inputmode='decimal' name='editProductPrice' step='0.01' value='{$pRow['cena']}' class='form-control form-control-sm mb-1'>";
                    
                    echo "<select name='editProductType' class='form-control form-control-sm mb-1'>";
                    $typesQuery = "SELECT id, typ FROM typy_produktow";
                    $typesRes = mysqli_query($conn, $typesQuery);
                    while($tRow = mysqli_fetch_array($typesRes)) {
                        $sel = ($tRow['id'] == $pRow['typ']) ? 'selected' : '';
                        echo "<option value='{$tRow['id']}' $sel>{$tRow['typ']}</option>";
                    }
                    echo "</select>";
                    
                    echo "<input type='file' name='editProductImage' accept='.jpg, .jpeg, .png' class='form-control form-control-sm mb-2'>";
                    echo "<button type='submit' name='updateProductBtn' class='btn btn-warning btn-sm'>Zapisz</button>";
                    echo "</form>";
                    
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>
        </div>

        <div id="createCoupon" class="adminPanelSection d-flex flex-column align-items-center card p-4 shadow bg-light rounded d-none">
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
                $products = $_POST['selectedProducts'];

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

    <div id="deleteCoupon" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
        <h2 class="mb-4">Usuń kupon</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCouponBtn'])) {
            $couponId = intval($_POST['couponId']);
            
            $deleteProductsQuery = "DELETE FROM kupony_produkty WHERE id_kuponu = $couponId";
            mysqli_query($conn, $deleteProductsQuery);

            $deleteCouponQuery = "DELETE FROM kupony WHERE id = $couponId";
            mysqli_query($conn, $deleteCouponQuery);
            // musialem zamiast headera zrobic to w js jaka kara
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?tab=deleteCoupon';</script>";
            exit();
        }
        ?>
        <div class="d-flex flex-wrap justify-content-center w-100">
            <?php
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
                while($row = mysqli_fetch_array($coupons)) {
                    $images = explode(',', $row['zdjecia']);
                    echo '<div class="couponBox d-flex flex-column align-items-center m-3 p-3">';
                    echo "<div class='d-flex justify-content-center w-100'>";
                    foreach ($images as $image) {
                        echo "<img src='src/$image' alt='{$row['nazwa']}' class='w-25 m-2' style='max-height: 50px;'>";
                    }
                    echo '</div>';
                    echo "<h3>$row[nazwa]</h3>za jedyne $row[cena]zł!";
                    
                    echo "<form action='' method='POST' style='margin-top: 10px;' onsubmit='return confirm(\"Na pewno chcesz usunąć ten kupon?\")'>";
                    echo "<input type='hidden' name='couponId' value='{$row['id']}'>";
                    echo "<button type='submit' name='deleteCouponBtn' class='btn btn-danger btn-sm'>Usuń kupon</button>";
                    echo "</form>";
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-info mt-5" role="alert">Brak dostępnych kuponów.</div>';
            }
            ?>
        </div>
    </div>

    <div id="editCoupon" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
        <h2 class="mb-4">Modyfikuj kupon</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCouponBtn'])) {
            $cId = intval($_POST['editCouponId']);
            $cCode = $_POST['editCouponCode'] ?? '';
            $cDiscount = $_POST['editCouponDiscount'] ?? '';
            $cProducts = $_POST['editSelectedProducts'] ?? '';

            if ($cId && $cCode && $cDiscount !== '') {
                $updateCouponQuery = "UPDATE kupony SET nazwa = ?, cena = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $updateCouponQuery)) {
                    mysqli_stmt_bind_param($stmt, 'sii', $cCode, $cDiscount, $cId);
                    if (mysqli_stmt_execute($stmt)) {
                        
                        $deleteOldRel = "DELETE FROM kupony_produkty WHERE id_kuponu = $cId";
                        mysqli_query($conn, $deleteOldRel);

                        if (!empty($cProducts)) {
                            $productArray = explode(',', $cProducts);
                            $stmt2 = mysqli_prepare($conn, "INSERT INTO kupony_produkty (id_kuponu, id_produktu) VALUES (?, ?)");
                            foreach ($productArray as $productId) {
                                $pid = intval($productId);
                                mysqli_stmt_bind_param($stmt2, "ii", $cId, $pid);
                                mysqli_stmt_execute($stmt2);
                            }
                            mysqli_stmt_close($stmt2);
                        }

                        echo '<div class="alert alert-success mb-3">Kupon został pomyślnie zaktualizowany.</div>';
                    } else {
                        echo '<div class="alert alert-danger mb-3">Błąd zapisu bazy danych.</div>';
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
        ?>
        <div class="d-flex flex-wrap justify-content-center w-100">
            <?php
            $query = "SELECT 
                        kupony.id, 
                        kupony.nazwa, 
                        kupony.cena,
                        GROUP_CONCAT(produkty.id) AS produkty_ids,
                        GROUP_CONCAT(produkty.zdjecie) AS zdjecia 
                        FROM kupony 
                        LEFT JOIN kupony_produkty ON kupony.id = kupony_produkty.id_kuponu
                        LEFT JOIN produkty ON kupony_produkty.id_produktu = produkty.id
                        GROUP BY kupony.id ORDER BY kupony.id DESC";
            
            $couponsEdit = mysqli_query($conn, $query);
            if (mysqli_num_rows($couponsEdit) > 0) {
                while($row = mysqli_fetch_array($couponsEdit)) {
                    echo '<div class="couponBox d-flex flex-column align-items-center m-3 p-3" style="width: 250px;">';
                    
                    echo "<div class='d-flex justify-content-center flex-wrap w-100 mb-2'>";
                    if (!empty($row['zdjecia'])) {
                        $images = explode(',', $row['zdjecia']);
                        foreach ($images as $image) {
                            echo "<img src='src/$image' alt='produkt' class='w-25 m-1' style='max-height: 40px;'>";
                        }
                    }
                    echo '</div>';

                    echo "<form action='#' method='POST' class='w-100 d-flex flex-column'>";
                    echo "<input type='hidden' name='editCouponId' value='{$row['id']}'>";
                    
                    echo "<label class='mb-1 small text-muted text-start w-100'>Nazwa kuponu:</label>";
                    echo "<input type='text' name='editCouponCode' value='".htmlspecialchars($row['nazwa'])."' class='form-control form-control-sm mb-2' required>";
                    
                    echo "<label class='mb-1 small text-muted text-start w-100'>Cena (zł):</label>";
                    echo "<input type='number' step='0.01' name='editCouponDiscount' value='{$row['cena']}' class='form-control form-control-sm mb-2' required>";
                    
                    echo "<button type='button' class='btn btn-secondary btn-sm mb-2 openProductPickerEdit' data-coupon-id='{$row['id']}' data-selected='{$row['produkty_ids']}'>";
                    echo "Dodaj/Zmień produkty";
                    echo "</button>";
                    
                    echo "<div class='selectedProductsPreviewEdit small mb-2 text-muted' id='previewEdit_{$row['id']}'>";
                    if(!empty($row['produkty_ids'])) {
                        $currIds = explode(',', $row['produkty_ids']);
                        foreach($currIds as $cId) {
                            echo "<span class='badge bg-primary m-1'>ID: $cId</span>";
                        }
                    }
                    echo "</div>";

                    echo "<input type='hidden' name='editSelectedProducts' id='inputEdit_{$row['id']}' value='{$row['produkty_ids']}'>";

                    echo "<button type='submit' name='updateCouponBtn' class='btn btn-warning btn-sm w-100'>Zapisz</button>";
                    echo "</form>";
                    
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-info" role="alert">Brak dostępnych kuponów do edycji.</div>';
            }
            ?>
        </div>
    </div>

    <div id="manageOrders" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
        <h2 class="mb-4">Zarządzaj zamówieniami</h2>
        <?php
            if(isset($_POST['setStatusFinishedBtn'])) {
                $orderId = intval($_POST['setStatusFinished']);
                $updateQuery = "UPDATE zamowienia SET status = 2 WHERE numer_zamowienia = $orderId";
                mysqli_query($conn, $updateQuery);
            }

            if(isset($_POST['setStatusCanceledBtn'])) {
                $orderId = intval($_POST['setStatusCanceled']);
                $updateQuery = "UPDATE zamowienia SET status = 3 WHERE numer_zamowienia = $orderId";
                mysqli_query($conn, $updateQuery);
            }

            if(isset($_POST['deleteOrderBtn'])) {
                $orderId = intval($_POST['deleteOrder']);
                $deleteQuery = "DELETE FROM zamowienia_produkty WHERE numer_zamowienia = $orderId";
                mysqli_query($conn, $deleteQuery);
                $deleteQuery = "DELETE FROM zamowienia WHERE numer_zamowienia = $orderId";
                mysqli_query($conn, $deleteQuery);
            }

            $query = "SELECT zamowienia.numer_zamowienia, uzytkownicy.nazwa_uzytkownika, zamowienia.data, status_zamowienia.status FROM zamowienia JOIN uzytkownicy ON zamowienia.uzytkownik_id = uzytkownicy.id JOIN status_zamowienia ON zamowienia.status = status_zamowienia.id ORDER BY zamowienia.data DESC";
            $orders = mysqli_query($conn, $query);
            while($row = mysqli_fetch_array($orders)) {
                echo "<div class='card mb-2 p-2 w-100 d-flex flex-row align-items-center justify-content-between'>";
                echo "<div>";
                    echo "<strong>Zamówienie #{$row['numer_zamowienia']}</strong><br>";
                    echo "Użytkownik: {$row['nazwa_uzytkownika']}<br>";
                    echo "Data: {$row['data']}<br>";
                    echo "Status: {$row['status']}";
                    echo "</div>";

                    echo "<form method='POST' action=''>";
                        echo "<input type='hidden' name='setStatusFinished' value='{$row['numer_zamowienia']}'>";
                        echo "<button type='submit' name='setStatusFinishedBtn' class='btn btn-success m-2'>Zakończ</button>";

                        echo "<input type='hidden' name='setStatusCanceled' value='{$row['numer_zamowienia']}'>";
                        echo "<button type='submit' name='setStatusCanceledBtn' class='btn btn-warning m-2'>Anuluj</button>";

                        echo "<input type='hidden' name='deleteOrder' value='{$row['numer_zamowienia']}'>";
                        echo "<button type='submit' name='deleteOrderBtn' class='btn btn-danger m-2'>Usuń</button>";
                    echo "</form>";
                echo "</div>";
            }
        ?>
    </div>

    <div id="manageUsers" class="adminPanelSection d-flex flex-column align-items-center d-none card p-4 shadow bg-light rounded">
        <h2 class="mb-4">Zarządzaj użytkownikami</h2>
        <?php
            if(isset($_POST['deleteUserBtn'])){
                $userIdToDelete = intval($_POST['deleteUserId']);

                $stmt = mysqli_prepare($conn, "DELETE FROM uzytkownicy WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $userIdToDelete);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            if(isset($_POST['changeUserStatusBtn'])) {
                $userIdToChange = intval($_POST['changeUserStatus']);

                $currentStatus = 0;
                $stmt = mysqli_prepare($conn, "SELECT dostep FROM uzytkownicy WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $userIdToChange);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $currentStatus);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                $newStatus = ($currentStatus == 1) ? 2 : 1;

                $updateStmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET dostep = ? WHERE id = ?");
                mysqli_stmt_bind_param($updateStmt, "ii", $newStatus, $userIdToChange);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
            }

            $query = "SELECT uzytkownicy.id, uzytkownicy.nazwa_uzytkownika, uzytkownicy.Email, uzytkownicy.telefon, dostep.dostep FROM uzytkownicy join dostep on uzytkownicy.dostep = dostep.id ORDER BY uzytkownicy.id ASC";
            $users = mysqli_query($conn, $query);
            while($row = mysqli_fetch_array($users)) {
                echo "<div class='card mb-2 p-2 w-100 d-flex flex-row flex-wrap align-items-center justify-content-between'>";
                echo "<div>";
                    echo "<strong>{$row['nazwa_uzytkownika']}</strong><br>";
                    echo "Email: {$row['Email']}<br>";
                    echo "Telefon: {$row['telefon']}<br>";
                    echo "Dostęp: " . $row['dostep'];
                    echo "</div>";

                    echo "<div class='d-flex flex-column flex-md-row gap-2'>";
                        if(intval($row['id']) !== intval($_SESSION['user_id'])) {
                            echo "<form method='POST' action=''>";
                            echo "<input type='hidden' name='changeUserStatus' value='{$row['id']}'>";
                            echo "<button type='submit' name='changeUserStatusBtn' class='btn btn-primary m-2'>Zmień status na " . ($row['dostep'] == "użytkownik" ? "administrator" : "użytkownik") . "</button>";
                            echo "</form>";
                        }
                        
                        if ($row['dostep'] != "administrator") {
                            echo "<form method='POST' action='' onsubmit='return confirm(\"Na pewno chcesz usunąć tego użytkownika?\")'>";
                            echo "<input type='hidden' name='deleteUserId' value='{$row['id']}'>";
                            echo "<button type='submit' name='deleteUserBtn' class='btn btn-danger'>Usuń</button>";
                            echo "</form>";
                        }
                    echo "</div>";
                echo "</div>";
            }
        ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        const createProductNav = document.querySelector('.createProductNav');
        const deleteProductNav = document.querySelector('.deleteProductNav');
        const editProductNav = document.querySelector('.editProductNav');
        const createCouponNav = document.querySelector('.createCouponNav');
        const deleteCouponNav = document.querySelector('.deleteCouponNav');
        const editCouponNav = document.querySelector('.editCouponNav');
        
        const manageOrdersNav = document.querySelector('.manageOrdersNav');
        const manageUsersNav = document.querySelector('.manageUsersNav');

        const createProductSection = document.getElementById('createProduct');
        const deleteProductSection = document.getElementById('deleteProduct');
        const editProductSection = document.getElementById('editProduct');
        const createCouponSection = document.getElementById('createCoupon');
        const deleteCouponSection = document.getElementById('deleteCoupon');
        const editCouponSection = document.getElementById('editCoupon');

        deleteCouponNav.addEventListener('click', () => {
            showTab('deleteCoupon');
        });

        createProductNav.addEventListener('click', () => {
            showTab('createProduct');
        });

        deleteProductNav.addEventListener('click', () => {
            showTab('deleteProduct');
        });

        editProductNav.addEventListener('click', () => {
            showTab('editProduct');
        });

        createCouponNav.addEventListener('click', () => {
            showTab('createCoupon');
        });

        editCouponNav.addEventListener('click', () => {
            showTab('editCoupon');
        });
        const manageOrdersSection = document.getElementById('manageOrders');
        const manageUsersSection = document.getElementById('manageUsers');

        createProductNav.addEventListener('click', () => showTab('createProduct'));
        deleteProductNav.addEventListener('click', () => showTab('deleteProduct'));
        editProductNav.addEventListener('click', () => showTab('editProduct'));
        createCouponNav.addEventListener('click', () => showTab('createCoupon'));
        deleteCouponNav.addEventListener('click', () => showTab('deleteCoupon'));
        editCouponNav.addEventListener('click', () => showTab('editCoupon'));
        manageOrdersNav.addEventListener('click', () => showTab('manageOrders'));
        manageUsersNav.addEventListener('click', () => showTab('manageUsers'));

        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('categoryFilter') && urlParams.get('categoryFilter') !== '') {
            showTab('deleteProduct');
        }
        if (urlParams.has('editCategoryFilter') && urlParams.get('editCategoryFilter') !== '') {
            showTab('editProduct');
        }
        if (urlParams.has('tab')) {
            showTab(urlParams.get('tab'));
        }

        function showTab(tab) {
            createProductSection.classList.add('d-none');
            deleteProductSection.classList.add('d-none');
            editProductSection.classList.add('d-none');
            createCouponSection.classList.add('d-none');
            deleteCouponSection.classList.add('d-none');
            editCouponSection.classList.add('d-none');
            manageOrdersSection.classList.add('d-none');
            manageUsersSection.classList.add('d-none');

            if (tab === 'createProduct') createProductSection.classList.remove('d-none');
            if (tab === 'deleteProduct') deleteProductSection.classList.remove('d-none');
            if (tab === 'editProduct') editProductSection.classList.remove('d-none');
            if (tab === 'createCoupon') createCouponSection.classList.remove('d-none');
            if (tab === 'deleteCoupon') deleteCouponSection.classList.remove('d-none');
            if (tab === 'editCoupon') editCouponSection.classList.remove('d-none');
            if (tab === 'manageOrders') manageOrdersSection.classList.remove('d-none');
            if (tab === 'manageUsers') manageUsersSection.classList.remove('d-none');

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
        let currentEditingCouponId = null;

        openBtn.addEventListener('click', () => {
            currentEditingCouponId = null;
            const val = selectedProductsInput.value;
            selected = val ? val.split(',') : [];
            modal.classList.remove('d-none');
            loadProducts();
        });

        document.addEventListener('click', function(e) {
            if(e.target && e.target.classList.contains('openProductPickerEdit')) {
                currentEditingCouponId = e.target.getAttribute('data-coupon-id');
                const targetInput = document.getElementById('inputEdit_' + currentEditingCouponId);
                const val = targetInput.value;
                selected = val ? val.split(',') : [];
                modal.classList.remove('d-none');
                loadProducts();
            }
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
                    
                    const checkboxes = productList.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(cb => {
                        if(selected.includes(cb.value)) {
                            cb.checked = true;
                        }
                    });
                });
        }

        function toggleProduct(id, name) {
            id = String(id);
            if (selected.includes(id)) {
                selected = selected.filter(x => x !== id);
            } else {
                selected.push(id);
            }

            updateSelected();
        }

        function updateSelected() {
            if (currentEditingCouponId === null) {
                selectedProductsInput.value = selected.join(',');
                preview.innerHTML = selected.map(id =>
                    `<span class="badge bg-primary m-1">ID: ${id}</span>`
                ).join('');
            } else {
                const targetInput = document.getElementById('inputEdit_' + currentEditingCouponId);
                const targetPreview = document.getElementById('previewEdit_' + currentEditingCouponId);
                
                targetInput.value = selected.join(',');
                targetPreview.innerHTML = selected.map(id =>
                    `<span class="badge bg-primary m-1">ID: ${id}</span>`
                ).join('');
                
                const triggerBtn = document.querySelector(`.openProductPickerEdit[data-coupon-id="${currentEditingCouponId}"]`);
                if(triggerBtn) triggerBtn.setAttribute('data-selected', selected.join(','));
            }
        }

        document.getElementById('confirmProducts').addEventListener('click', () => {
            modal.classList.add('d-none');
        });

        const savedTab = localStorage.getItem('activeTab');
        if (savedTab && !urlParams.has('categoryFilter') && !urlParams.has('editCategoryFilter') && !urlParams.has('tab')) {
            showTab(savedTab);
        }
    </script>
</body>
</html>

<?php
    mysqli_close($conn);
?>