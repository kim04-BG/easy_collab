<?php
    

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

    $conn->close();


    

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil et Visibilité</title>
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .profile-header {
            background-image: linear-gradient(135deg, #0388ee, #ff0550); /* Dégradé de couleurs */
            padding: 50px;
            text-align: center;
            color: white;
            
        }
        .profil-header img {
            border-radius: 50%;
            border: 5px solid white;
            margin-bottom: -30px;
            margin-right: 550px;
            
        }
        .profile-header h2 {
            margin-top: 0px;
        }
        .profile-section {
            margin: 20px 0;
        }
        .profile-section h5 {
            margin-bottom: 10px;
            margin-right: 10px;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .card-text {
            font-size: 1rem;
        }
        .icon-text {
            display: flex;
            align-items: center;
        }
        .icon-text i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <div class="row">
                <div class="col-5">
                    <div class="profil-header">
                    <img src="assets/img/profil.jpg" alt="Photo de profil" width="200" height="200">
                    </div>
                </div>
                <div class="col-7">
                <h2>Profil et visibilité</h2>
                <p>Gérez vos informations personnelles avec une vue générale.</p>
                <h1 class=""><B><?php echo htmlspecialchars($nom . ' ' . $prenom); ?></B></h1>
                </div>
            </div>
        
            
        </div>
        
        
        <div class="profile-section">
            <h5>Photo de profil et image d'en-tête</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="assets/img/profil.jpg" alt="Photo de profil" width="100" height="100">
                        </div>
                        <div class="col-md-10">
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h5>À propos de vous</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Nom</strong></div>
                        <div class="col-md-6"><?php echo htmlspecialchars($nom . ' '); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Prenom</strong></div>
                        <div class="col-md-6"><?php echo htmlspecialchars($prenom . ' '); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h5>Contact</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4"><strong>Adresse e-mail</strong></div>
                        <div class="col-md-6"><?php echo htmlspecialchars($email); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
