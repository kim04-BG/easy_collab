<?php

            include "connexiondb.php";

            if(isset($_POST['sinscrire'])){
                $nom= $_POST['nom'];
                $prenom= $_POST['prenom'];
                $email= $_POST['email'];
                $mdp= $_POST['mdp'];
            

                $query="INSERT INTO `utilisateur`(`nom`, `prenom`, `email`, `mot_de_passe`)
                 VALUES ('$nom','$prenom','$email','$mdp')";
                $res= mysqli_query($conn, $query);
            if ($res){
                echo 'Inscription fait avec succès';
            }
                else{  
                echo 'Echec , problème technique';
            }   
            }

            ?>