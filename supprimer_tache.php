<?php
session_start();
include "connexiondb.php"; // Inclut le fichier de connexion à la base de données

// Récupérer l'ID de la tâche et de projet depuis l'URL
if (isset($_GET['id']) && isset($_GET['id_projet'])) {
    $id_tache = $_GET['id'];
    $id_projet = $_GET['id_projet'];
} else {
    // Rediriger ou afficher un message d'erreur si l'ID de la tâche ou du projet est manquant
    echo "<script>alert('ID de la tâche ou du projet manquant.'); window.location.href='mestaches.php?id_projet=$id_projet';</script>";
    exit();
}

// Supprimer la tâche
$query_delete = "DELETE FROM tache WHERE id_tache = ?";
$stmt = $conn->prepare($query_delete);
if (!$stmt) {
    die("Erreur de préparation de la requête : " . $conn->error);
}
$stmt->bind_param("i", $id_tache);
if ($stmt->execute()) {
    echo "<script>alert('Tâche supprimée avec succès.'); window.location.href='mestaches.php?id_projet=$id_projet';</script>";
} else {
    echo "<script>alert('Erreur lors de la suppression de la tâche.'); window.location.href='mestaches.php?id_projet=$id_projet';</script>";
}
$stmt->close();
exit();
?>
