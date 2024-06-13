<?php
// Récupérer les projets du chef de projet connecté avec leurs tâches
$query_projets = "
    SELECT p.id_projet, p.titre, p.description, p.date_debut, p.date_fin, p.statut_projet, p.id_methode, m.libelle AS libelle_methode, 
           t.id_tache, t.titre AS tache_titre, t.statut_tache 
    FROM projet p
    LEFT JOIN methode m ON p.id_methode = m.id_methode
    LEFT JOIN tache t ON p.id_projet = t.id_projet
    WHERE p.id_chef = ?
";
$stmt = $conn->prepare($query_projets);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result_projets = $stmt->get_result();

$projets = [];
while ($row = $result_projets->fetch_assoc()) {
    $projet_id = $row['id_projet'];
    if (!isset($projets[$projet_id])) {
        $projets[$projet_id] = [
            'id_projet' => $row['id_projet'],
            'titre' => $row['titre'],
            'description' => $row['description'],
            'date_debut' => $row['date_debut'],
            'date_fin' => $row['date_fin'],
            'statut_projet' => $row['statut_projet'],
            'libelle_methode' => $row['libelle_methode'],
            'taches' => []
        ];
    }
    if (!is_null($row['id_tache'])) {
        $projets[$projet_id]['taches'][] = [
            'id_tache' => $row['id_tache'],
            'titre' => $row['tache_titre'],
            'statut_tache' => $row['statut_tache']
        ];
    }
}
$stmt->close();

function calculerProgression($taches) {
  $total_taches = count($taches);
  if ($total_taches === 0) {
      return 0;
  }

  $taches_terminees = 0;
  foreach ($taches as $tache) {
      if ($tache['statut_tache'] === 'termine') {
          $taches_terminees++;
      }
  }

  return ($taches_terminees / $total_taches) * 100;
}

foreach ($projets as &$projet) {
  $projet['progression'] = calculerProgression($projet['taches']);
}
?>



<!-- Nouvelle ligne pour les graphiques en courbe -->
<div class="row">
                              <h2 class="card-title card-title-dash" style="margin-bottom: 3%;"><b>Progression des Projets</b></h2>
                              <?php
                              $index = 0;
                              foreach ($projets as $projet) :
                                  $color_class = $color_classes[$index % count($color_classes)];
                                  $index++;
                                  $progress_json = json_encode($projet['progression']); // Convertir les données en JSON pour les utiliser dans JavaScript
                              ?>
                                  <div class="col-md-6 mb-4">
                                      <div class="card <?php echo $color_class; ?>">
                                          <div class="card-body">
                                              <h5 class="card-title"><?php echo htmlspecialchars($projet['titre']); ?> - Progression</h5>
                                              <div class="chart-container">
                                                  <canvas id="progressChart<?php echo $projet['id_projet']; ?>"></canvas>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  
                              <?php endforeach; ?>
                          </div>





                          <?php foreach ($projets as $projet) : ?>
          <script>
              var ctx<?php echo $projet['id_projet']; ?> = document.getElementById('progressChart<?php echo $projet['id_projet']; ?>').getContext('2d');
              var progressData<?php echo $projet['id_projet']; ?> = <?php echo $progress_json; ?>;
              var labels<?php echo $projet['id_projet']; ?> = ["Début", "Avancement"];
              var data<?php echo $projet['id_projet']; ?> = [0, progressData<?php echo $projet['id_projet']; ?>];

              var chart<?php echo $projet['id_projet']; ?> = new Chart(ctx<?php echo $projet['id_projet']; ?>, {
                  type: 'line',
                  data: {
                      labels: labels<?php echo $projet['id_projet']; ?>,
                      datasets: [{
                          label: 'Progression du Projet',
                          backgroundColor: 'rgba(75, 192, 192, 0.2)',
                          borderColor: 'rgba(75, 192, 192, 1)',
                          data: data<?php echo $projet['id_projet']; ?>
                      }]
                  },
                  options: {
                      scales: {
                          x: {
                              type: 'category',
                              labels: labels<?php echo $projet['id_projet']; ?>
                          },
                          y: {
                              beginAtZero: true,
                              max: 100
                          }
                      }
                  }
              });
          </script>
        <?php endforeach; ?>