<?php
session_start();

$db = mysqli_connect("localhost", "root", "", "szama");
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $message = 'Wypełnij wszystkie wymagane pola.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Podaj poprawny adres e-mail.';
    } elseif (strlen($password) < 6) {
        $message = 'Hasło musi mieć co najmniej 6 znaków.';
    } else {
        // ochrona od sql injection
        mysqli_set_charset($db, 'utf8mb4');
        $checkStatement = mysqli_prepare($db, "SELECT id FROM uzytkownicy WHERE nazwa_uzytkownika = ? OR Email = ?");
        mysqli_stmt_bind_param($checkStatement, 'ss', $username, $email);
        mysqli_stmt_execute($checkStatement);
        mysqli_stmt_store_result($checkStatement);

        if (mysqli_stmt_num_rows($checkStatement) > 0) {
            $message = 'Nazwa użytkownika lub e-mail są już zajęte.';
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            //
            $insertStatement = mysqli_prepare($db, "INSERT INTO uzytkownicy (nazwa_uzytkownika, Email, haslo, dostep, status) VALUES (?, ?, ?, 1, 1)");
            mysqli_stmt_bind_param($insertStatement, 'sss', $username, $email, $hash);

            if (mysqli_stmt_execute($insertStatement)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = mysqli_insert_id($db);
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;

                mysqli_stmt_close($checkStatement);
                mysqli_stmt_close($insertStatement);
                mysqli_close($db);

                header('Location: menu.php');
                exit;
            }

            $message = 'Wystąpił błąd podczas rejestracji. Spróbuj ponownie.';
            mysqli_stmt_close($insertStatement);
        }

        mysqli_stmt_close($checkStatement);
    }
}
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
        <a href="" class="me-auto">Kupony</a>
        <a href="menu.php"><img src="src/zegowska_szama-logo.png" alt="logo" class=""></a>
        <a href="logIn.php" class="ms-auto">Logowanie</a>
        <a href="signIn.php" class="m-3" id="SignInBtn">Rejestracja</a>
    </header>
    <main class="flex-grow-1 d-flex justify-content-center align-items-center flex-row">
        <div id="SignIn" class="m-5">
            <?php if ($message !== '') {
                echo '<div class="alert alert-warning" role="alert">' . htmlspecialchars($message) . '</div>';
            } ?>
            <form action="#" method="post" class="d-flex justify-content-center align-items-center flex-column">
                <h2>Rejestracja</h2>
                <label for="SignInName">Nazwa Konta</label><input type="text" id="SignInName">
                <label for="SignInEmail">E-mail</label><input type="email" id="SignInEmail">
                <label for="SignInTel">Numer Telefonu</label><input type="tel" id="SignInTel" pattern="[0-9]{9}">
                <label for="SignInPass">Hasło</label><input type="password" id="SignInPass">
                <input type="submit" value="Utwórz konto" id="CreateAccountBtn">
                <sub>Masz już konto? <a href="logIn.php" id="LogInAccExistHref">Zaloguj się</a></sub>
            </form>
        </div>
        <div class="m-5">
            <h1>Zamawiaj szybciej z kontem ZEG SZAMA!</h1>
            <ul>
                <li>Zapisz ostatnie zamówienia</li>
                <li>Otrzymuj dostęp do rabatów i promocji</li>
            </ul>
        </div>
    </main>
    <footer class="d-flex align-items-center justify-content-center p-2">
        <div class="me-auto">
            Zamów przez telefon <br>
            +48 123 456 789
        </div>
        <div class="position-absolute">
            <a href="" class="m-2">Nowości</a>
            <a href="" class="m-2">O nas</a>
            <a href="" class="m-2">Kontakt</a>
        </div>
        <a href="https://www.zs4.oswiata.tychy.pl/" class="ms-auto">Strona zegu</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
<?php
    mysqli_close($db); 
?>