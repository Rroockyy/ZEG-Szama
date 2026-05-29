<?php
    session_start();
    $conn = mysqli_connect("localhost", "root", "", "szama");

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($login === '' || $password === '') {

            $message = 'Wypełnij wszystkie pola.';

        } else {

            mysqli_set_charset($conn, 'utf8mb4');

            $statement = mysqli_prepare(
                $conn,
                "SELECT id, nazwa_uzytkownika, Email, telefon, haslo 
                FROM uzytkownicy 
                WHERE nazwa_uzytkownika = ? OR Email = ?"
            );

            mysqli_stmt_bind_param($statement, 'ss', $login, $login);
            mysqli_stmt_execute($statement);

            $result = mysqli_stmt_get_result($statement);

            if ($user = mysqli_fetch_assoc($result)) {

                if (password_verify($password, $user['haslo'])) {

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['nazwa_uzytkownika'];
                    $_SESSION['phone'] = $user['telefon'];
                    $_SESSION['email'] = $user['Email'];
                    $_SESSION['logged_in'] = true;

                    header("Location: menu.php");
                    exit;

                } else {

                    $message = 'Nieprawidłowe hasło.';
                }

            } else {

                $message = 'Użytkownik nie istnieje.';
            }

            mysqli_stmt_close($statement);
        }
    }

    mysqli_close($conn);
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
    <main class="flex-grow-1 d-flex justify-content-center align-items-center flex-column">
        <img src="src/zeg.png" alt="logo zegu" id="zeg-background"> 
        <div id="LogIn" class="my-3">
            <?php
                if ($message !== '') {
                    echo '<div class="alert alert-warning">' .
                        htmlspecialchars($message) .
                        '</div>';
                }
            ?>
            <form action="logIn.php" method="post" class="d-flex justify-content-center align-items-center flex-column row-gap-2">
                <h2>Logowanie</h2>
                <label for="LogInEmail" style="align-self: flex-start;">E-mail</label><input type="email" id="LogInEmail" name="login" required>
                <label for="LogInPass" style="align-self: flex-start;">Hasło</label><input type="password" id="LogInPass" name="password" required>
                <!-- <a href="" id="ForgotPass"><sub>Nie pamiętasz hasła?</sub></a> -->
                <input type="submit" value="Zaloguj się" id="LogInBtn">
                <sub>Nie masz konta? <a href="signIn.php" id="LogInAccExistHref">Zarejestruj się</a></sub>
            </form>
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
</body>
</html>