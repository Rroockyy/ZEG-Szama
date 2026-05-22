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
            echo '<div class="ms-auto text-end">';
                echo '<h4 class="ms-auto font-weight-bold">Zalogowano jako:  ' . htmlspecialchars($_SESSION['username']) . '</h4>';
                echo '<a href="logout.php" class="ms-3" id="logOut">Wyloguj się</a>';
            echo '</div>';
        } else {
            echo '<a href="logIn.php" class="ms-auto">Logowanie</a>';
            echo '<a href="signIn.php" class="m-3" id="SignInBtn">Rejestracja</a>';
        }
        ?>
    </header>
    <main class="flex-grow-1 d-flex align-items-center flex-column flex-fill">
        <h1 class="mt-5">O nas</h1>
        <p class="mt-3 text-center w-75 fs-3">
            Strona Zegowskiego Sklepiku szkolnego została przygotowana przez Ksawerego Berk i Antoniego Dzięgielewskiego. Aplikacja została stworzona w ramach projektu szkolnego w klasie 3 w roku 2026 w okesie kwiecień-czerwiec.
        </p>
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
</body>
</html>

<?php
    mysqli_close($conn);
?>