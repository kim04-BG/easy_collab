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

    // Récupération des projets et leurs méthodes associées
    $query_projets = "
        SELECT p.id_projet, p.titre, p.description, p.date_debut, p.date_fin, p.statut_projet, m.libelle AS libelle_methode 
        FROM projet p 
        JOIN methode m ON p.id_methode = m.id_methode 
        WHERE p.id_chef = ?";
    $stmt_projets = $conn->prepare($query_projets);
    $stmt_projets->bind_param("i", $id_utilisateur);
    $stmt_projets->execute();
    $result_projets = $stmt_projets->get_result();
    $projets = $result_projets->fetch_all(MYSQLI_ASSOC);
    $stmt_projets->close();

    // Récupération des projets où l'utilisateur est membre mais pas le créateur
$query_projets_membre = "
SELECT p.id_projet, p.titre, p.description, p.date_debut, p.date_fin, p.statut_projet, m.libelle AS libelle_methode 
FROM projet p 
JOIN methode m ON p.id_methode = m.id_methode 
JOIN equipe e ON p.id_projet = e.id_projet 
WHERE e.id_utilisateur = ? AND p.id_chef != ?";
$stmt_projets_membre = $conn->prepare($query_projets_membre);
$stmt_projets_membre->bind_param("ii", $id_utilisateur, $id_utilisateur);
$stmt_projets_membre->execute();
$result_projets_membre = $stmt_projets_membre->get_result();
$projets_membre = $result_projets_membre->fetch_all(MYSQLI_ASSOC);
$stmt_projets_membre->close();
    $conn->close();


    

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
      .card-color-1 {
          background-color: #f8d7da; /* Couleur rouge clair */
      }

      .card-color-2 {
          background-color: #d4edda; /* Couleur vert clair */
      }

      .card-color-3 {
          background-color: #d1ecf1; /* Couleur bleu clair */
      }

      .card-color-4 {
          background-color: #fff3cd; /* Couleur jaune clair */
      }

      .card-color-5 {
          background-color: #cce5ff; /* Couleur bleu foncé */
      }

      .card-color-6 {
            background-color: #fcb7b7; /* Couleur bleu foncé */
      }

      .card-color-7 {
            background-color: #fba3f9; /* Couleur bleu foncé */
      }

      .card-title {
          color: #343a40; /* Couleur du texte */
          font-weight: bold;
      }

      .card-text { font-size: 16px; margin-bottom: 10px; }
        .btn-edit {
            position: absolute;
            bottom: 15px;
            right: 15px;
          }
          .chart-container {
            position: relative;
            height: 40vh;
            width: 100%;
        }
        
    </style>
</head>
<body class="with-welcome-text">

<div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row ">
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
                                <p class="mb-0 fw-medium float-start">Vous avez 4 nouvelles notifications</p>
                                <span class="badge badge-pill badge-primary float-end">Voir tout</span>
                            </a>
                            <a class="dropdown-item preview-item py-3">
                                <div class="preview-thumbnail">
                                    <i class="mdi mdi-alert m-auto text-primary"></i>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject fw-normal text-dark mb-1">Erreur d'application</h6>
                                    <p class="fw-light small-text mb-0">Juste maintenant</p>
                                </div>
                            </a>
                            <a class="dropdown-item preview-item py-3">
                                <div class="preview-thumbnail">
                                    <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject fw-normal text-dark mb-1">Paramètres</h6>
                                    <p class="fw-light small-text mb-0">Message privé</p>
                                </div>
                            </a>
                            <a class="dropdown-item preview-item py-3">
                                <div class="preview-thumbnail">
                                    <i class="mdi mdi-airballoon m-auto text-primary"></i>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject fw-normal text-dark mb-1">Nouvelle inscription utilisateur</h6>
                                    <p class="fw-light small-text mb-0">Il y a 2 jours</p>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="img-xs rounded-circle" src="assets/img/profil.jpg" alt="Profile image">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="assets/img/P.jpg" alt="Profile image">
                                <p class="mb-1 mt-3 fw-semibold"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                                <p class="fw-light text-muted mb-0"><?php echo htmlspecialchars($email); ?></p>
                            </div>
                            <a class="dropdown-item" href="profil.phhp"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> Mon Profil </a>
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
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas"  id="sidebar">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="tableau_de_bord_chef.php">
                    <i class="mdi mdi-grid-large menu-icon"></i>
                    <span class="menu-title">Tableau de bord</span>
                  </a>
                </li>
                <li class="nav-item nav-category">ÉLÉMENTS DE L’INTERFACE UTILISATEUR</li>
                
                <li class="nav-item">
                    <a class="nav-link"  href="publication.php" aria-expanded="false" aria-controls="tables">
                      <i class="menu-icon mdi mdi-table"></i>
                      <span class="menu-title">Publication</span>
                    </a>
                </li>
                <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
                          <i class="menu-icon mdi mdi-layers-outline"></i>
                          <span class="menu-title">Projets</span>
                          <i class="menu-arrow"></i>
                      </a>
                      <div class="collapse" id="icons">
                      
                        <ul class="nav flex-column sub-menu">
                        <?php foreach ($projets as $projet): ?>
                          <li class="nav-item">
                             <a class="nav-link" href="equipe.php?id_projet=<?php echo $projet['id_projet']; ?>">
                             <?php echo htmlspecialchars($projet['titre']); ?> 
                              </a>
                          </li>
                          <?php endforeach; ?>
                        </ul>
                      
                      </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                        <i class="menu-icon mdi mdi-card-text-outline"></i>
                        <span class="menu-title">Projet Associer</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="form-elements">
                        <ul class="nav flex-column sub-menu">
                            <?php if (count($projets_membre) > 0): ?>
                                <?php foreach ($projets_membre as $projet): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="equipeM.php?id_projet=<?php echo $projet['id_projet']; ?>">
                                            <?php echo htmlspecialchars($projet['titre']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link" href="#">Aucun projet associé</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                  <a class="nav-link"  href="profil.php" aria-expanded="false" aria-controls="auth">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">Mon Profil</span>
                    
                  </a>
                </li>
              </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
              <div class="content-wrapper">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="home-tab">
                      <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                        <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Profil</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="resumetache.php" role="tab" aria-selected="false"></a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#demographics" role="tab" aria-selected="false"></a>
                          </li>
                        </ul>
                        <div>
                          <div class="btn-wrapper">
                            <a href="ajoutprojet.php" class="btn btn-primary text-white me-0"><i class="fas fa-plus"></i>Créer</a>
                          </div>
                        </div>
                      </div>
                      
                      <div class="tab-content tab-content-basic">
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                        <?php include "mon_profil.php"; ?>

                        </div>
                        <div class="tab-pane fade show active" id="resumetache" role="tabpanel" aria-labelledby="profile-tab">
                            <!-- Contenu de l'onglet Tableau de bord -->
                            
                             <!-- Intégration de la page resumetache.php -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- content-wrapper ends -->
              <!-- partial:partials/_footer.html -->
              
              <!-- partial -->
            </div>
            <!-- main-panel ends -->
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
        <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
        <script src="assets/js/dashboard.js"></script>
        <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
        <!-- End custom js for this page-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.0/js/bootstrap.bundle.min.js"></script>

        
        
    
</body>
</html>
