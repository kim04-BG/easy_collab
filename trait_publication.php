<?php

include "connexiondb.php";


// Récupérer les projets du chef de projet connecté
$query_projets = "SELECT id_projet, titre FROM projet WHERE id_chef = ?";
$stmt = $conn->prepare($query_projets);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result_projets = $stmt->get_result();
$projets = $result_projets->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Traiter le formulaire de publication
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_projet = $_POST['id_projet'];
    $titre = htmlspecialchars($_POST['titre']);
    $contenu = htmlspecialchars($_POST['contenu']);

    $stmt = $conn->prepare("INSERT INTO publications (id_projet, titre, contenu) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $id_projet, $titre, $contenu);
        if ($stmt->execute()) {
            echo '<script>alert("Publication créée avec succès"); window.location.href = "publication.php";</script>';
        } else {
            echo '<script>alert("Erreur lors de la création de la publication");</script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("Erreur lors de la préparation de la requête");</script>';
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB - Tableau de bord - Résumé des tâches</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #ff007f;
            color: white;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .page-title {
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .bg-gradient-rose-red {
            background: linear-gradient(90deg, #ff007f, #ff0040);
            color: white;
        }
    </style>
</head>
<body class="with-welcome-text">
    
<div class="container">
<?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>
        <h2 class="mt-4">Créer une Publication</h2>
        <form method="POST" action="trait_publication.php">
            <div class="form-group">
                <label for="id_projet">Sélectionner un Projet</label>
                <select class="form-control" id="id_projet" name="id_projet" required>
                    <option value="">Sélectionner un projet</option>
                    <?php foreach ($projets as $projet) : ?>
                        <option value="<?php echo $projet['id_projet']; ?>"><?php echo htmlspecialchars($projet['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="titre">Titre de la Publication</label>
                <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre de la publication" required>
            </div>
            <div class="form-group">
                <label for="contenu">Contenu de la Publication</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" placeholder="Entrez le contenu de la publication" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Créer la Publication</button>
        </form>
    </div>

<!-- container-scroller -->
<!-- plugins:js -->
<script src="assets/vendors/chart.js/chart.umd.js"></script>
<script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/template.js"></script>
<script src="assets/js/settings.js"></script>
<script src="assets/js/hoverable-collapse.js"></script>
<script src="assets/js/todolist.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->

</body>
</html>
