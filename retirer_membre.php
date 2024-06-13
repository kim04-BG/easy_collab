<?php
session_start();
include "connexiondb.php";

if (!isset($_SESSION['id_utilisateur']) || !isset($_POST['id_projet']) || !isset($_POST['id_membre'])) {
    header("Location: connexion.php");
    exit();
}

$id_projet = $_POST['id_projet'];
$id_membre = $_POST['id_membre'];

// Supprimer le membre de l'équipe
$query = "DELETE FROM equipe WHERE id_projet = ? AND id_utilisateur = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_projet, $id_membre);

if ($stmt->execute()) {
    echo '<script>alert("Membre retiré avec succès."); window.location.href = "equipe.php?id_projet='.$id_projet.'";</script>';
} else {
    echo '<script>alert("Erreur lors du retrait du membre."); window.location.href = "equipe.php?id_projet='.$id_projet.'";</script>';
}

$stmt->close();
$conn->close();
?>
