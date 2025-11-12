   <?php
   $password = 'elsa';
   $hash = '$2y$13$BZLGAHRBZpua66lRpRBpnuNPjru6OLpQinWSBGRYWarxpwAlqb0HK';

   if (password_verify($password, $hash)) {
       echo "Mot de passe valide\n";
   } else {
       echo "Mot de passe invalide\n";
   }