<?php

include "connexiondb.php";



$id_utilisateur = $_SESSION['id_utilisateur'];
$id_projet = $_GET['id_projet'];

// Récupérer les informations du projet
$query_projet = "SELECT titre FROM projet WHERE id_projet = ?";
$stmt = $conn->prepare($query_projet);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($titre_projet);
$stmt->fetch();
$stmt->close();

// Récupérer les membres de l'équipe
$query_membres = "SELECT utilisateur.id_utilisateur, utilisateur.nom, utilisateur.prenom FROM equipe JOIN utilisateur ON equipe.id_utilisateur = utilisateur.id_utilisateur WHERE equipe.id_projet = ? AND equipe.id_chef != utilisateur.id_utilisateur";
$stmt = $conn->prepare($query_membres);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$result_membres = $stmt->get_result();

// Récupérer le chef de projet
$query_chef = "SELECT utilisateur.nom, utilisateur.prenom FROM projet JOIN utilisateur ON projet.id_chef = utilisateur.id_utilisateur WHERE projet.id_projet = ?";
$stmt = $conn->prepare($query_chef);
$stmt->bind_param("i", $id_projet);
$stmt->execute();
$stmt->bind_result($nom_chef, $prenom_chef);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Équipe</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            background-color: darkgray;
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
<body>
    <div class="d-sm-flex align-items-center justify-content-between border-bottom">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"></li>
            <li class="nav-item"></li>            
            <li class="nav-item"></li>             
        </ul>                  
        <div>
            <div class="btn-wrapper">
                <a href="ajout2equipe.php?id_projet=<?php echo $id_projet; ?>" class="btn btn-primary" role="button"><i class="fas fa-plus"></i>Ajouter Membre</a>
            </div>
        </div>                  
    </div>                      
                                     
<h2 class="text-center"><B><?php echo htmlspecialchars($titre_projet); ?></B></h2><br>
    <div class="container"> 
        <h2 class="page-title">Détails de l'Équipe</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-gradient-rose-red">
                        Chef de Projet
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="bg-gradient-rose-red">
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($nom_chef); ?></td>
                                    <td><?php echo htmlspecialchars($prenom_chef); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Membres de l'Équipe
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_membres->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                                        <td>
                                            <form method="post" action="retirer_membre.php" style="display:inline;">
                                                <input type="hidden" name="id_projet" value="<?php echo $id_projet; ?>">
                                                <input type="hidden" name="id_membre" value="<?php echo $row['id_utilisateur']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Retirer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
