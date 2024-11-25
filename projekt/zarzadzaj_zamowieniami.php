<?php
require_once 'database.php';
session_start();
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$wiadomosc = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_order'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];

    $query = $conn->prepare("UPDATE zamowienia SET email = ?, imie = ?, nazwisko = ? WHERE id_zamowienia = ?");
    $query->bind_param("sssi", $email, $imie, $nazwisko, $id);

    if ($query->execute()) {
        $wiadomosc = "Dane zamówienia zostały zapisane!";
    } else {
        $wiadomosc = "Błąd podczas edycji zamówienia!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $id = $_POST['id'];

    $query = $conn->prepare("DELETE FROM zamowienia WHERE id_zamowienia = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        $wiadomosc = "Zamówienie zostało usunięte!";
    } else {
        $wiadomosc = "Błąd podczas usuwania zamówienia!";
    }
}

$query = $conn->prepare("
    SELECT z.id_zamowienia,z.username,z.email,z.imie,z.nazwisko,z.data_zamowienia,z.id_uzytkownika,p.nazwa AS nazwa_produktu,zp.ilosc,
        CONCAT(za.ulica,' ',za.nr_domu,'/',za.nr_mieszkania,',',za.miasto,' ',za.kod_pocztowy) AS adres
    FROM 
        zamowienia z
    LEFT JOIN 
        zamowienia_produkty zp ON z.id_zamowienia=zp.id_zamowienia
    LEFT JOIN 
        produkty p ON zp.id_produktu = p.id_produktu
    LEFT JOIN 
        zamowienia_adresy za ON z.id_zamowienia=za.id_zamowienia
");
$query->execute();
$orders = $query->get_result();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie zamówieniami</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/78fa2015f8.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light text-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-elements-font">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="./css/img/Tech.png" width="30" height="30" class="d-inline-block align-top brand-logo-sizing"
                    alt="Jurzyk">
                <a class="navbar-brand navbar-custom-font"><span class="logop1">B</span><span
                        class="logop2">Y</span><span class="logop3">T</span><span class="logop4">E</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="./index.php">Strona główna <span class="sr-only">(Aktualnie
                                    włączone)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./about.php">O nas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./kontakt.php">Kontakt</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <a class="nav-link" href="login.php">
                        <i class="fa-solid fa-user fa-xl fa-fw navicon"></i>
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-cart-shopping fa-xl fa-fw navicon"></i>
                    </a>
                </div>
        </div>
    </nav>
    <br>
    <br>
    <br>
    <div class="containerArrow">
        <a class="strzalka" href="panel.php"><i class="arrow right"></i>Wróć</a>
    </div>
    <div class="container mt-5">
        <?php if (!empty($wiadomosc)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($wiadomosc); ?>
            </div>
        <?php endif; ?>
        <h2 class="text-center">Lista zamówień</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Data zamówienia</th>
                    <th>Produkt</th>
                    <th>Ilość</th>
                    <th>Adres</th>
                    <th>ID użytkownika</th>
                    <th>Zmiany</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id_zamowienia']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['imie']); ?></td>
                        <td><?php echo htmlspecialchars($order['nazwisko']); ?></td>
                        <td><?php echo htmlspecialchars($order['data_zamowienia']); ?></td>
                        <td><?php echo htmlspecialchars($order['nazwa_produktu']); ?></td>
                        <td><?php echo htmlspecialchars($order['ilosc']); ?></td>
                        <td><?php echo htmlspecialchars($order['adres']); ?></td>
                        <td><?php echo htmlspecialchars($order['id_uzytkownika']); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $order['id_zamowienia']; ?>">
                                <input type="email" name="email" value="<?php echo htmlspecialchars($order['email']); ?>"
                                    class="form-control form-control-sm mb-1" required>
                                <input type="text" name="imie" value="<?php echo htmlspecialchars($order['imie']); ?>"
                                    class="form-control form-control-sm mb-1" required>
                                <input type="text" name="nazwisko"
                                    value="<?php echo htmlspecialchars($order['nazwisko']); ?>"
                                    class="form-control form-control-sm mb-1" required>
                                <button type="submit" name="edit_order" class="btn btn-warning btn-sm w-100">Edytuj</button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $order['id_zamowienia']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger btn-sm w-100">Usuń</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>