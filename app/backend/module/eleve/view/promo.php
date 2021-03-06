<?php if ($voirMatiere) : ?>
   <h1>Résultats de la promotion pour la matière « <?php echo $matiere; ?> »</h1>
   <p><strong>Moyenne de la matière</strong> :
      <?php
      if (!empty($moyenneMatiere)) :
         echo $moyenneMatiere;
         ?>/20
         <?php
      else :
         ?> Indisponible
   <?php endif; ?></p>
   <h2>Examens</h2>
   <p><a href="/étudiant/perso/<?php echo $module; ?>/<?php echo $matiere; ?>" class="button blueButton">Mes résultats</a></p>
   <table>
      <thead>
         <tr>
            <th>Intitulé</th>
            <th>Date</th>
            <th>Moyenne promotion</th>
         </tr>
      </thead>
      <tbody>
         <?php
         if (!empty($listeDesExamens)) :
            foreach ($listeDesExamens as $examen) :
               ?>
               <tr>
                  <td><?php echo $examen['libelle']; ?></td>
                  <td><?php echo $examen['date']; ?></td>
                  <td><?php echo $examen['moyennePromo']; ?>/20</td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="4">Aucun examen</td>
            </tr>
         <?php
         endif;
         ?>
      </tbody>
   </table>
   <p><a href="/étudiant/promo/<?php echo $module; ?>">Retour au module <?php echo $module; ?></a></p>
<?php elseif ($voirModule) : ?>
   <h1>Résultats de la promotion pour le module « <?php echo $module; ?> »</h1>
   <p><strong>Moyenne du module</strong> :
      <?php
      if (!empty($moyenneModule)) :
         echo $moyenneModule;
         ?>/20
         <?php
      else :
         ?> Indisponible
      <?php endif; ?></p>
   <h2>Matières</h2>
   <p><a href="/étudiant/perso/<?php echo $module; ?>" class="button blueButton">Mes résultats</a></p>
   <ul>
      <?php
      if (!empty($listeDesMatieres)) :
         foreach ($listeDesMatieres as $matiere) :
            ?>
            <li><a href="/étudiant/promo/<?php echo $module; ?>/<?php echo $matiere['libelle']; ?>"><?php echo $matiere['libelle']; ?></a> :
               <?php
               if (!empty($matiere['moyenne'])) :
                  echo $matiere['moyenne'];
                  ?>/20
               <?php else :
                  ?>
                  Moyenne indisponible
               <?php
               endif;
               ?>
            </li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucune matière</li>
      <?php
      endif;
      ?>
   </ul>
   <p><a href="/étudiant/promo">Retour aux résultats de votre promotion</a></p>
<?php elseif ($afficherListe) : ?>
   <h1>Liste des étudiants de votre promotion</h1>
   <p>Voici la liste des étudiants de votre promotion. Sélectionnez l'un d'entre eux pour consulter ses résultats.</p>
   <table>
      <thead>
         <tr>
            <th>Login</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php
         if (!empty($listeDesEtudiants)) :
            foreach ($listeDesEtudiants as $etudiant) :
               ?>
               <tr>
                  <td><?php echo $etudiant['login']; ?></td>
                  <td><?php echo $etudiant['nom']; ?></td>
                  <td><?php echo $etudiant['prenom']; ?></td>
                  <td><a href="/étudiant/<?php echo $etudiant['idUtil']; ?>"><img src="/img/admin/go_notes.png" alt="Résultats étudiant" title="Consulter les résultats de cet étudiant" /></a></td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="5">Aucun étudiant</td>
            </tr>
         <?php endif; ?>
      </tbody>
   </table>
   <p><a href="/étudiant/">Retour à l'accueil de votre espace personnel</a></p>
<?php else : ?>
   <h1>Résultats de votre promotion</h1>
   <p><strong>Moyenne générale</strong> :
      <?php
      if (!empty($moyenneGenerale)) :
         echo $moyenneGenerale;
         ?>/20
         <?php
      else :
         ?> Indisponible
      <?php endif; ?></p>
   <h2>Modules</h2>
   <p><a href="/étudiant/perso" class="button blueButton">Mes résultats</a></p>
   <ul>
      <?php
      if (!empty($listeDesModules)) :
         foreach ($listeDesModules as $module) :
            ?>
            <li>
               <a href="/étudiant/promo/<?php echo $module['libelle']; ?>"><?php echo $module['libelle']; ?></a> :
               <?php
               if (!empty($module['moyenne'])) :
                  echo $module['moyenne'];
                  ?>/20
               <?php else :
                  ?>
                  Moyenne indisponible
               <?php
               endif;
               ?>
            </li>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module</li>
      <?php endif; ?>
   </ul>
   <p><a href="/étudiant/">Retour à l'accueil de votre espace personnel</a></p>
<?php endif; ?>