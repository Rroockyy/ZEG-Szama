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
    <link rel="icon" href="zeg.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="min-vh-100 d-flex flex-column">
    <header class="sticky-top d-flex align-items-center justify-content-center p-2"> 
        <a href="menu.php" class="m-3">Menu</a>
        <a href="coupons.php" class="me-auto">Kupony</a>
        <a href="menu.php"><img src="src/zegowska_szama-logo.png" alt="logo" class=""></a>
        <a href="logIn.php" class="ms-auto">Logowanie</a>
        <a href="signIn.php" class="m-3" id="SignInBtn">Rejestracja</a>
    </header>
    <main class="flex-grow-1 d-flex justify-content-center align-items-center flex-column">
        <img src="src/zeg.png" alt="logo zegu" id="zeg-background"> 
        <div id="LogIn" class="m-5">
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
                <a href="" id="ForgotPass"><sub>Nie pamiętasz hasła?</sub></a>
                <div><input type="checkbox" id="RememberPass"><label for="RememberPass">Zapamiętaj hasło</label></div>
                <input type="submit" value="Zaloguj się" id="LogInBtn">
                <sub>Nie masz konta? <a href="signIn.php" id="LogInAccExistHref">Zarejestruj się</a></sub>
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
</body>
</html>