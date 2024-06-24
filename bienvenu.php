<?php
session_start(); // Démarrer la session

include "connexiondb.php"; // Inclure le fichier de connexion à la base de données

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];

// Récupérer le nom de l'utilisateur à partir de la base de données
$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateur WHERE id_utilisateur = ?");
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom_utilisateur, $prenom_utilisateur);
$stmt->fetch();
$stmt->close();

// Redirection basée sur le rôle
if ($role == 'chef de projet') {
    $tableau_de_bord_url = "tableau_de_bord_chef.php"; // Tableau de bord du chef de projet
} else if ($role == 'membre equipe') {
    $tableau_de_bord_url = "tableau_de_bord_membre.php"; // Tableau de bord du membre
} else {
    // Si le rôle n'est pas reconnu, rediriger vers une page par défaut ou afficher un message d'erreur
    $tableau_de_bord_url = "error.php";
}

// Message de bienvenue avec le nom de l'utilisateur
$message_bienvenue = "Bienvenue sur EASYCOLLAB $prenom_utilisateur $nom_utilisateur ";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bienvenue</title>
    <!-- plugins:css -->
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
      
.bien {
  background-image: linear-gradient(135deg, #0388ee, #ff0550); /* Dégradé de couleurs */
  color: #fff; /* Couleur du texte */
  padding: 100px 20px;
  text-align: center;
}
.btn-primary {
      background-color: #0279f8;
      border-color: #7b7bfb;
    }
    .btn-primary:hover {
      background-color: #f64a87;
      border-color: #f47fc1;
    }

    </style>
  </head>
  <body>

  <div class="container-scroller bien">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center text-center error-page bien">
            <div class="row flex-grow">
                <div class="col-lg-7 mx-auto ">
                    <section id="c-pro">
                        <div class="">
                            <img src="assets/img/LOGO.png" alt="Logo de la plateforme" height="50" style="margin-top: -10%;">
                            <h3 class="primary-text mb-2" style="font-size: 300%; font-family: High Tower Text;"><?php echo $message_bienvenue; ?></h3>
                            <p class="lead">Lancez-vous dans la création de votre projet avec facilité et inspiration</p>
                        </div>
                    </section>
                </div>
                <div class="row mt-5">
                    <div class="col-3  mt-xl-2"></div>
                    <div class="col-6  mt-xl-2">
                        <!-- Rediriger directement vers le tableau de bord du chef de projet -->
                        <a href="tableau_de_bord_chef.php" class="btn btn-primary" role="button">Accéder au Tableau de Bord</a>
                    </div>
                    <div class="col-3  mt-xl-2"></div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>

    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
  </body>
</html>
