<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role']) || !isset($_SESSION['id_projet'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté ou si l'ID du projet n'est pas défini
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_SESSION['id_projet'];

// Récupérer les informations de l'utilisateur connecté
$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur, $email_utilisateur);
$stmt->fetch();
$stmt->close();

// Récupérer les informations du projet en cours
$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($nom_projet);
$stmt->fetch();
$stmt->close();

if (isset($_POST['ajouter'])) {
    // Récupération des données pour la tâche 1
    $nom1 = htmlspecialchars($_POST['nom1']);
    $description1 = htmlspecialchars($_POST['description1']);
    $date_debut1 = htmlspecialchars($_POST['date_debut1']);
    $date_fin1 = htmlspecialchars($_POST['date_fin1']);
    $statut1 = "en_attente"; // Définir le statut par défaut

    // Insertion de la tâche 1 dans la base de données
    $stmt1 = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssssiiss", $nom1, $description1, $date_debut1, $date_fin1, $statut1, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
    $res1 = $stmt1->execute();
    $stmt1->close();

    // Récupération des données pour la tâche 2
    $nom2 = htmlspecialchars($_POST['nom2']);
    $description2 = htmlspecialchars($_POST['description2']);
    $date_debut2 = htmlspecialchars($_POST['date_debut2']);
    $date_fin2 = htmlspecialchars($_POST['date_fin2']);
    $statut2 = "en_attente"; // Définir le statut par défaut

    // Insertion de la tâche 2 dans la base de données
    $stmt2 = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssiiss", $nom2, $description2, $date_debut2, $date_fin2, $statut2, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
    $res2 = $stmt2->execute();
    $stmt2->close();

    // Récupération des données pour la tâche 3
    $nom3 = htmlspecialchars($_POST['nom3']);
    $description3 = htmlspecialchars($_POST['description3']);
    $date_debut3 = htmlspecialchars($_POST['date_debut3']);
    $date_fin3 = htmlspecialchars($_POST['date_fin3']);
    $statut3 = "en_attente"; // Définir le statut par défaut

    // Insertion de la tâche 3 dans la base de données
    $stmt3 = $conn->prepare("INSERT INTO `tache`(`titre`, `description`, `date_debut`, `date_fin`, `statut_tache`, `id_projet`, `id_utilisateur`, `id_chef`, `id_ass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt3->bind_param("sssssiiss", $nom3, $description3, $date_debut3, $date_fin3, $statut3, $id_projet, $id_utilisateur, $id_utilisateur, $id_utilisateur);
    $res3 = $stmt3->execute();
    $stmt3->close();

    // Vérification de l'insertion pour chaque tâche
    if ($res1 && $res2 && $res3) {
        echo '<script>alert("Toutes les tâches ont été ajoutées avec succès."); window.location.href = "ajoutequipe.php";</script>';
    } else {
        echo '<script>alert("Erreur : Une ou plusieurs tâches n\'ont pas pu être ajoutées.");</script>';
    }

    // Fermeture de la connexion
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet"> <!-- Lien vers la police Montserrat -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body id="background" background="">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
          <a class="navbar-brand" href="acceuil.html">
            <img src="assets/img/LOGO.png" alt="Logo de la plateforme" height="30">
            <B><B>EASYCOLLAB</B></B>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
      
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item">
                <a class="nav-link" href="connexion.html">Connexion</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="inscription.html">Inscription</a>
              </li>
            </ul>
          </div>
        </nav>
    </header>

    <section id="c-pro">
        <div class="">
          <h2><B> <?php echo htmlspecialchars($nom_projet); ?></B></h2>
          <h2 class="">Transformez votre projet avec chaque tâche ajoutée <B><?php echo htmlspecialchars($prenom_utilisateur); ?></B></h2>
          <p class="lead">Ajoutez des étapes concrètes pour concrétiser votre vision, une tâche à la fois</p>
        </div>
    </section>


    <div class="container custom-container">
        <div class="row">
            <div class="col-md-6">
                <h2><B>Ajouter des Tâches</B></h2>
                <form id="tasks-form" method="POST">
                    
                    <!-- Tâche 1 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 1</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom1" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description1" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task1-start-date">Date de Début :</label>
                                <input type="date" class="form-control" name="date_debut1" required>
                            </div>
                            <div class="col-6">
                                <label for="task1-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" name="date_fin1" required>
                            </div>
                        </div><br>
                    </div>
                    <!-- Tâche 2 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 2</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom2" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description2" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task2-start-date">Date de Début :</label>
                                <input type="date" class="form-control" name="date_debut2" required>
                            </div>
                            <div class="col-6">
                                <label for="task2-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" name="date_fin2" required>
                            </div>
                        </div><br>
                        
                    </div>
                    <!-- Tâche 3 -->
                    <div class="form-group">
                        <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Tâche 3</B></h5>
                        <input type="text" class="form-control" placeholder="Nom de la tâche" name="nom3" required><br>
                        <textarea class="form-control" rows="3" placeholder="Description de la tâche" name="description3" required></textarea>
                        <div class="row">
                            <div class="col-6">
                                <label for="task3-start-date">Date de Début :</label>
                                <input type="date" class="form-control" name="date_debut3" required>
                            </div>
                            <div class="col-6">
                                <label for="task3-end-date">Date de Fin :</label>
                                <input type="date" class="form-control" name="date_fin3" required>
                            </div>
                        </div><br>
                        
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="ajouter">Ajouter des Tâches</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <h2><B>Aperçu des Tâches</B></h2>
                <div class="card"> 
                <!-- Tableau illustrant l'aperçu des tâches -->
                    <img src="assets/img/tache.jpg" alt="">
                </div>
                <div class="card"> 
                <!-- Tableau illustrant l'aperçu des tâches -->
                    <img src="assets/img/tache.jpg" alt="">
                </div>
            
            </div>
        </div>
    </div>


  <script src="assets/js/jquery-3.6.0.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/bootstrap.bundle.js"></script>
  <script src="assets/js/index.js"></script>
</body>
</html>