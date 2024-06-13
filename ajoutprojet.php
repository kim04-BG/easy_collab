<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur, $email_utilisateur);
$stmt->fetch();
$stmt->close();

if (isset($_POST['creer'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $date_debut = htmlspecialchars($_POST['date_debut']);
    $date_fin = htmlspecialchars($_POST['date_fin']);
    $methode = htmlspecialchars($_POST['methode']);
    $statut = "en_attente"; // Définir le statut par défaut

    $stmt = $conn->prepare("INSERT INTO `projet`(`titre`, `description`, `date_debut`, `date_fin`, `statut_projet`, `id_methode`, `id_chef`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssis", $nom, $description, $date_debut, $date_fin, $statut, $methode, $id_utilisateur);
        if ($stmt->execute()) {
            // Récupérer l'ID du projet nouvellement créé
            $id_projet = $stmt->insert_id;
            // Stocker l'ID du projet dans la session
            $_SESSION['id_projet'] = $id_projet;
            echo '<script>alert("Projet ajouté avec succès"); window.location.href = "ajouttaches.php";</script>';
        } else {
            echo '<script>alert("Insertion incorrecte, Problème technique");</script>';
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
    <title>EASYCOLLAB</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="transition.js"></script>
</head>
<body id="background">
    <div id="page1" class="page active">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="acceuil.html">
                    <img src="assets/img/LOGO.png" alt="Logo de la plateforme" height="30">
                    <b>EASYCOLLAB</b>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="connexion.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inscription.php">Inscription</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <section id="c-pro">
            <div class="">
                <h2 class=""><b>Préparez-vous à donner vie à votre vision, <?php echo htmlspecialchars($prenom_utilisateur); ?>!</b></h2>
                <p class="lead">Lancez-vous dans la création de votre projet avec facilité et inspiration</p>
            </div>
        </section>

        <div class="container custom-container">
            <div class="row">
                <div class="col-md-6">
                    <h2><b>Nouveau Projet</b></h2>
                    <form id="project-form" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" name="nom" placeholder="Entrez le nom du projet" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="project-description" name="description" rows="3" placeholder="Entrez la description du projet" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="task1-start-date">Date de Début :</label>
                                    <input type="date" class="form-control" id="task1-start-date" name="date_debut" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="task1-end-date">Date de Fin :</label>
                                    <input type="date" class="form-control" id="task1-end-date" name="date_fin" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="project-method">Méthode de Gestion :</label>
                            <select class="form-control" id="project-method" name="methode" required>
                                <option value="">Méthode de Gestion</option>
                                <?php
                                include "connexiondb.php";
                                $sql = "SELECT * FROM `methode`";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='".$row["id_methode"]."'>".$row["libelle"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="creer">Créer un Projet</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h2><b>Aperçu du Tableau de Bord</b></h2>
                    <div class="preview-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tâche</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-primary">
                                    <td>1</td>
                                    <td>Exemple de Tâche 1</td>
                                    <td>En Cours</td>
                                </tr>
                                <tr class="table-success">
                                    <td>2</td>
                                    <td>Exemple de Tâche 2</td>
                                    <td>A Faire</td>
                                </tr>
                                <tr class="table-danger">
                                    <td>3</td>
                                    <td>Exemple de Tâche 3</td>
                                    <td>Terminé</td>
                                </tr>
                                <tr class="table-warning">
                                    <td>4</td>
                                    <td>Exemple de Tâche 4</td>
                                    <td>Terminé</td>
                                </tr>
                                <tr class="table-info">
                                    <td>5</td>
                                    <td>Exemple de Tâche 5</td>
                                    <td>Terminé</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><br><br>
    </div>
    <script src="assets/js/jquery-3.6.0.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
