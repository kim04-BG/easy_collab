<?php
session_start();
include "connexiondb.php";

// Assurez-vous que l'id_projet est passé dans l'URL
if (!isset($_GET['id_projet'])) {
    echo "ID du projet non spécifié.";
    exit;
}

$id_projet = $_GET['id_projet'];
$id_utilisateur = $_SESSION['id_utilisateur'];

$id_projet = $_POST['id_projet'];
$id_utilisateur = $_POST['id_utilisateur'];
$message = $_POST['message'];

$query = "INSERT INTO messages (id_projet, id_utilisateur, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $id_projet, $id_utilisateur, $message);
$stmt->execute();
$stmt->close();
?>
