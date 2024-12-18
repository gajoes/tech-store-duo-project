<?php
require_once 'database.php';
session_start();

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$wiadomosc = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $nazwa_kategorii = $_POST['nazwa_kategorii'];

    $query = $conn->prepare("INSERT INTO kategorie (nazwa_kategorii) VALUES (?)");
    $query->bind_param("s", $nazwa_kategorii);

    if ($query->execute()) {
        $wiadomosc = "Kategoria została dodana!";
    } else {
        $wiadomosc = "Błąd podczas dodawania kategorii!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $id_kategorii = $_POST['id_kategorii'];
    $nazwa_kategorii = $_POST['nazwa_kategorii'];

    $query = $conn->prepare("UPDATE kategorie SET nazwa_kategorii = ? WHERE id_kategorii = ?");
    $query->bind_param("si", $nazwa_kategorii, $id_kategorii);

    if ($query->execute()) {
        $wiadomosc = "Dane kategorii zostały zapisane!";
    } else {
        $wiadomosc = "Błąd podczas edycji kategorii!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $id_kategorii = $_POST['id_kategorii'];

    $query = $conn->prepare("DELETE FROM kategorie WHERE id_kategorii = ?");
    $query->bind_param("i", $id_kategorii);

    if ($query->execute()) {
        $wiadomosc = "Kategoria została usunięta!";
    } else {
        $wiadomosc = "Błąd podczas usuwania kategorii!";
    }
}

$query = $conn->prepare("SELECT id_kategorii, nazwa_kategorii FROM kategorie");
$szukaj=$_GET['szukaj']??'';
$query_szukaj="SELECT id_kategorii, nazwa_kategorii FROM kategorie";

if(!empty($szukaj)){
$szukaj_warunki='%'.$szukaj.'%';
$query_szukaj.=" WHERE nazwa_kategorii LIKE ? OR id_kategorii LIKE ?";
}

$query=$conn->prepare($query_szukaj);

if(!empty($szukaj)){
$query->bind_param("ss",$szukaj_warunki,$szukaj_warunki);
}
$query->execute();
$categories = $query->get_result();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie kategoriami</title>
    <link rel="stylesheet" href="style.css">
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
        <div class="card bg-white mb-5 text-dark text-center border-light shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Dodaj kategorię</h2>
                <form method="POST" class="row g-3">
                    <div class="col-md-12">
                        <input type="text" name="nazwa_kategorii" class="form-control" placeholder="Nazwa kategorii" pattern="[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]+" required>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" name="add_category" class="btn btn-primary w-50 buy">Dodaj
                            kategorię</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card bg-white mb-3 text-dark text-center border-light shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 justify-content-center">
            <div class="col-md-6">
                <input type="text" name="szukaj" class="form-control" placeholder="Wpisz, aby wyszukać..." value="<?php echo htmlspecialchars($_GET['szukaj'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 buy">Szukaj</button>
            </div>
        </form>
    </div>
</div>
        <h2 class="text-center">Lista kategorii</h2>
        <table class="table table-light table-hover border">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nazwa kategorii</th>
                    <th>Zmiany</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['id_kategorii']); ?></td>
                        <td><?php echo htmlspecialchars($category['nazwa_kategorii']); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_kategorii" value="<?php echo $category['id_kategorii']; ?>">
                                <input type="text" name="nazwa_kategorii" placeholder="Nazwa kategorii"
                                    value="<?php echo htmlspecialchars($category['nazwa_kategorii']); ?>"
                                    class="form-control form-control-sm mb-1" pattern="[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]+" required>
                                <button type="submit" name="edit_category"
                                    class="btn btn-warning btn-sm w-100 mb-2">Edytuj</button>
                            </form>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tą kategorię?');">
                                <input type="hidden" name="id_kategorii" value="<?php echo $category['id_kategorii']; ?>">
                                <button type="submit" name="delete_category"
                                    class="btn btn-danger btn-sm w-100">Usuń</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        
        body{
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container{
            max-width: 95%;
            margin: 0 auto;
        }

        table{
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td{
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th{
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .btn{
            margin-top: 5px;
        }

        .zmiany input, .zmiany select{
            margin-bottom: 5px;
        }

    </style>
</body>
</html>