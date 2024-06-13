@echo off
REM Aller dans le répertoire de votre projet
cd /d "C:\W\www\EASYCOLLAB"

REM Vérifier l'état du dépôt
git status

REM Ajouter tous les fichiers modifiés
git add .

REM Commettre les modifications avec un message
git commit -m "Mise a jour du projet effectuer"

REM Pousser les modifications vers le dépôt distant
git push origin main

pause
