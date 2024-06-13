<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYCOLLAB</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet"> <!-- Lien vers la police Montserrat -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body id="background" background="assets/img/BACK.jpg">
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
                <a class="nav-link" href="connexion.php">Connexion</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="inscription.php">Inscription</a>
              </li>
            </ul>
          </div>
        </nav>
    </header>

    <?php
include "connexiondb.php";

if(isset($_POST['sinscrire'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Hashage du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Utiliser des requêtes préparées pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, id_roles, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Erreur lors de la préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param("sssis", $nom, $prenom, $email, $role, $mot_de_passe_hash);

    if ($stmt->execute()) {
        // Insertion réussie, afficher un message de succès
        echo '<script>alert("Inscription faite avec succès"); window.location.href = "connexion.php";</script>';
    } else {
        echo '<script>alert("Insertion incorrecte, Problème technique");</script>';
    }

    $stmt->close(); // Fermer la requête préparée
    $conn->close(); // Fermer la connexion à la base de données
}
?>


    <div class="container mt-5">
      <div class="col-md-6 offset-md-3">
          <div class="card form">
              <div class="card-body">
                <div class="text-center">
                  <img src="assets/img/LOGO.png" alt="Logo de la plateforme" height="50" style="margin-top: -10%;">
                  <h5><B>EASYCOLLAB</B></h5><br>
                </div>
              <h5 class="text-center mb-4" style="color: rgb(2, 2, 160);"><B>Inscrivez-vous pour continuer</B></h5>
              <form id="registrationForm" method="POST">
    <div class="form-group">
        <input type="text" class="form-control" id="lastName" placeholder="Nom" name="nom" required>
        <div class="error-message" id="lastNameError"></div>
    </div>
    <div class="form-group">
        <input type="text" class="form-control" id="firstName" placeholder="Prénom" name="prenom" required>
        <div class="error-message" id="firstNameError"></div>
    </div>
    <div class="form-group">
        <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
        <div class="error-message" id="emailError"></div>
    </div>
    <div class="form-group">
        <select class="form-control" id="role" name="role" required>
        <option value="">Rôle</option>
        <?php
            // Connexion à la base de données
            include "connexiondb.php";

            // Requête pour sélectionner tous les logements disponibles
            $sql = "SELECT * FROM `roles`";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Afficher les options pour chaque logement
                while($row = $result->fetch_assoc()) {
                  echo "<option value='".$row["id_roles"]."'>".$row["libelle"]."</option>";
                }
            }
            $conn->close();
            ?>
        </select>
    </div>
    <div class="form-group">
        <input type="password" class="form-control" id="password" placeholder="Mot de passe" name="mot_de_passe" required>
        <div class="error-message" id="passwordError"></div>
    </div>
    <button type="submit" class="btn btn-primary btn-block" name="sinscrire">S'inscrire</button>
</form>

            </div>
          </div>
      </div>    
    </div><br><br>

    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <img src="assets/img/LOGO.png" alt="Logo" class="footer-logo">
            <p class="F"><B>EASYCOLLAB</B> - Plateforme de gestion de projets collaboratifs</p>
          </div>
          <div class="col-md-6 d-flex justify-content-end align-items-center">
            <!-- Liens du footer alignés à droite -->
            <a href="about.html" class="footer-link">À propos</a>
            <a href="contact.html" class="footer-link">Contact</a>
            <a href="privacy.html" class="footer-link">Politique de confidentialité</a>
          </div>
        </div>
        <hr class="footer-divider">
        <div class="row">
          <div class="col-md-6">
            <div class="social-icons">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          <div class="col-md-6 text-md-right">
            <p class="copyright">&copy; 2024 EASYCOLLAB. Tous droits réservés.</p>
          </div>
        </div>
      </div>
    </footer>

    <script src="assets/js/jquery-3.6.0.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/bootstrap.bundle.js"></script>

</body>
</html>