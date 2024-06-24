<?php
session_start(); // Démarrer la session

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: connexion.php");
    exit();
}

// Récupérer l'ID de l'utilisateur depuis la session
$id_utilisateur = $_SESSION['id_utilisateur'];

// Vérifier que l'ID du projet est passé via l'URL ou la session
if (isset($_GET['id_projet'])) {
    $id_projet = $_GET['id_projet'];
} elseif (isset($_SESSION['id_projet'])) {
    $id_projet = $_SESSION['id_projet'];
} else {
    // Gérer le cas où l'ID du projet n'est pas disponible
    echo "ID du projet manquant.";
    exit();
}

// Stocker l'ID du projet dans la session pour une utilisation future
$_SESSION['id_projet'] = $id_projet;

// Inclure le fichier de connexion à la base de données
include "connexiondb.php";

// Récupérer l'ID de l'utilisateur depuis la session
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur depuis la base de données
$query_utilisateur = "SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($query_utilisateur);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$stmt->bind_result($nom, $prenom, $email);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB - Tableau de bord chef</title>
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select.dataTables.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- endinject -->
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styletb.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
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
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
              <div class="me-3">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                  <span class="icon-menu"></span>
                </button>
              </div>
              <div>
                <a class="navbar-brand brand-logo" href="tableau_de_bord_chef.php">
                  <img src="assets/img/LOGO.png" alt="logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="tableau_de_bord_chef.php">
                  <img src="assets/img/LOGO.png" alt="logo" />
                </a>
              </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top">
            <ul class="navbar-nav">
                    <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Bonjour, <span class="text-black fw-bold"><?php echo htmlspecialchars($prenom); ?></span></h1>
                        <h3 class="welcome-sub-text">Votre résumé des détails de votre projet</h3>
                    </li>
                </ul>
              <ul class="navbar-nav ms-auto">
              <li class="nav-item d-none d-lg-block">
              <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                <span class="input-group-addon input-group-prepend border-right">
                  <span class="icon-calendar input-group-text calendar-icon"></span>
                </span>
                <input type="text" class="form-control">
              </div>
            </li>
                <li class="nav-item">
                  <form class="search-form" action="#">
                    <i class="icon-search"></i>
                    <input type="search" class="form-control" placeholder="Recherche ici" title="Recherche ici">
                  </form>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                    <i class="icon-bell"></i>
                    <span class="count"></span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
                    <a class="dropdown-item py-3 border-bottom">
                      <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>
                      <span class="badge badge-pill badge-primary float-end">View all</span>
                    </a>
                    <a class="dropdown-item preview-item py-3">
                      <div class="preview-thumbnail">
                        <i class="mdi mdi-alert m-auto text-primary"></i>
                      </div>
                      <div class="preview-item-content">
                        <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>
                        <p class="fw-light small-text mb-0"> Just now </p>
                      </div>
                    </a>
                    <a class="dropdown-item preview-item py-3">
                      <div class="preview-thumbnail">
                        <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                      </div>
                      <div class="preview-item-content">
                        <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>
                        <p class="fw-light small-text mb-0"> Private message </p>
                      </div>
                    </a>
                    <a class="dropdown-item preview-item py-3">
                      <div class="preview-thumbnail">
                        <i class="mdi mdi-airballoon m-auto text-primary"></i>
                      </div>
                      <div class="preview-item-content">
                        <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>
                        <p class="fw-light small-text mb-0"> 2 days ago </p>
                      </div>
                    </a>
                  </div>
                </li>
                <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                  <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="assets/img/profil.jpg" alt="Profile image"> </a>
                  <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                      <img class="img-md rounded-circle" src="assets/img/P.jpg" alt="Profile image">
                      <p class="mb-1 mt-3 fw-semibold"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                        <p class="fw-light text-muted mb-0"><?php echo htmlspecialchars($email); ?></p>
                    </div>
                    <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> Mon Profil <span class="badge badge-pill badge-danger">1</span></a>
                    <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                    <a class="dropdown-item" href="deconnexion.php"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Déconnexion</a>
                  </div>
                </li>
              </ul>
              <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                <span class="mdi mdi-menu"></span>
              </button>
            </div>
      </nav>
      <!-- partial -->
        <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="tableau_de_bord_chef.php">
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Tableau de bord</span>
                  </a>
                </li>
                <li class="nav-item nav-category">ÉLÉMENTS DE L’INTERFACE UTILISATEUR</li>
                <li class="nav-item">
                        <a class="nav-link" href="equipe.php?id_projet=<?php echo $id_projet; ?>" aria-expanded="false" aria-controls="tables">
                            <i class="menu-icon fa fa-group"></i>
                            <span class="menu-title">Equipe</span>
                        </a>
                    </li>
                <li class="nav-item">
                  <a class="nav-link"  href="mestaches.php?id_projet=<?php echo $id_projet; ?>" aria-expanded="false" aria-controls="ui-basic">
                    <i class="menu-icon mdi mdi-floor-plan"></i>
                    <span class="menu-title">Mes Tâches</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="message.php?id_projet=<?php echo $id_projet; ?>" aria-expanded="false" aria-controls="form-elements">
                    <i class="menu-icon fa fa-envelope"></i> 
                    <span class="menu-title">Boîte de reception</span>
                  </a>
                </li> 
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">User Pages</span>
                    <i class="menu-arrow"></i>
                  </a>
                  <div class="collapse" id="auth">
                    <ul class="nav flex-column sub-menu">
                      <li class="nav-item"> <a class="nav-link" href="pages/samples/blank-page.html"> Blank Page </a></li>
                      <li class="nav-item"> <a class="nav-link" href="pages/samples/error-404.html"> 404 </a></li>
                      <li class="nav-item"> <a class="nav-link" href="pages/samples/error-500.html"> 500 </a></li>
                      <li class="nav-item"> <a class="nav-link" href="pages/samples/login.html"> Login </a></li>
                      <li class="nav-item"> <a class="nav-link" href="pages/samples/register.html"> Register </a></li>
                    </ul>
                  </div>
                </li>
              </ul>
            </nav>  
        <!-- partial -->
            <div class="main-panel">
              <div class="content-wrapper">
                <div class="row">
                    <div class="tab-pane fade show active" id="resumetache" role="tabpanel" aria-labelledby="profile-tab">
                                <!-- Contenu de l'onglet Tableau de bord -->
                                <?php include "traitM_equipe.php"; ?> <!-- Intégration de la page resumetache.php -->
                    </div>

                </div>
              </div>
        <!-- main-panel ends -->
             </div>
        </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="assets/js/chart.js"></script>
    <!-- End custom js for this page-->
  </body>
</html>