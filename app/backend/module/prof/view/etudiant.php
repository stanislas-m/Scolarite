<?php if ($showProfil) : ?>
   <h1>Profil étudiant</h1>
   <p><strong>Login</strong> : <?php echo $etudiant['login']; ?></p>
   <p><strong>Nom</strong> : <?php echo $etudiant['nom']; ?></p>
   <p><strong>Prénom</strong> : <?php echo $etudiant['prenom']; ?></p>
   <h2>Informations principales</h2>
   <p><strong>Promotion</strong> : <a href="/admin/<?php echo $etudiant['promo']; ?>"><?php echo $etudiant['promo']; ?></a></p>
   <p><strong>Numéro d'étudiant</strong> : <?php echo $etudiant['numEtudiant']; ?></p>
   <p><strong>Année de redoublement</strong> :
      <?php
      if (!empty($etudiant['anneeRedouble'])) :
         echo $etudiant['anneeRedouble'];
      else :
         ?>
         Aucune
      <?php endif; ?>
   </p>
   <p><strong>Moyenne générale</strong> :
      <?php
      if (!empty($etudiant['moyenneGenerale'])) :
         echo $etudiant['moyenneGenerale'];
         ?>/20
      <?php else : ?>
         Moyenne indisponible
      <?php endif; ?>
   </p>
   <h2>Modules</h2>
   <ul>
      <?php
      if (!empty($etudiant['listeDesModules'])) :
         foreach ($etudiant['listeDesModules'] as $module) :
            ?>
            <li><a href="/prof/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/matières"><?php echo $module['libelle']; ?></a>
               <?php if (!empty($module['moyennePromo'])) : ?>
                  (<strong>Moyenne de l'étudiant</strong> :
                  <?php
                  if (!empty($module['moyenneEleve'])) :
                     echo $module['moyenneEleve'];
                     ?>/20
                  <?php else : ?>
                     Moyenne indisponible
                  <?php endif; ?> ; 
                  <strong>Moyenne promo</strong> :
                  <?php echo $module['moyennePromo']; ?>/20)
               <?php endif; ?>
            </li>
            <ul>
               <?php
               if (!empty($module['listeDesMatieres'])) :
                  foreach ($module['listeDesMatieres'] as $matiere) :
                     ?>
                     <li><a href="/prof/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/<?php echo $matiere['libelle']; ?>"><?php echo $matiere['libelle']; ?></a>
                        <?php if (!empty($matiere['moyennePromo'])) : ?>
                           (<strong>Moyenne de l'étudiant</strong> :
                           <?php
                           if (!empty($matiere['moyenneEleve'])) :
                              echo $matiere['moyenneEleve'];
                              ?>/20
                           <?php else : ?>
                              Moyenne indisponible
                           <?php endif; ?> ; 
                           <strong>Moyenne promo</strong> :
                           <?php echo $matiere['moyennePromo']; ?>/20)
                        <?php endif; ?>
                     </li>
                     <ul>
                        <?php
                        if (!empty($matiere['listeDesExamens'])) :
                           foreach ($matiere['listeDesExamens'] as $examen) :
                              ?>
                              <li><?php echo $examen['libelle']; ?> (<strong>Note</strong> :
                                 <?php
                                 if (!empty($examen['note'])) :
                                    echo $examen['note'];
                                    ?>/20
                                 <?php else : ?>
                                    Absence justifiée
                                 <?php endif; ?> ; <strong>Moyenne promo</strong> : <?php echo $examen['moyennePromo']; ?>/20)
                              </li>

                              <?php
                           endforeach;
                        else :
                           ?>
                           <li>Aucun examen</li>
                        <?php endif; ?>
                     </ul>
                     <?php
                  endforeach;
               else :
                  ?>
                  <li>Aucune matière</li>
               <?php endif; ?>
            </ul>
            <?php
         endforeach;
      else :
         ?>
         <li>Aucun module</li>
      <?php endif; ?>
   </ul>
   <p><a href="/prof/<?php echo $etudiant['promo']; ?>/étudiants">Retour à la liste des étudiants</a></p>
   <?php
else :
   ?>
   <h1>Liste des étudiants <?php echo $prefixPromo . $promo; ?></h1>
   <table>
      <thead>
         <tr>
            <th>Login</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Action</th>
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
                  <td><a href="/prof/étudiant/<?php echo $etudiant['idUtil']; ?>/profil"><img src="/img/prof/go_user.png" alt="Profil élève" title="Consulter le profil de cet élève" /></a></td>
               </tr>
               <?php
            endforeach;
         else :
            ?>
            <tr>
               <td colspan="4">Aucun étudiant dans cette promotion</td>
            </tr>
         <?php endif; ?>
      </tbody>
   </table>

   <p><a href="/prof/<?php echo $promo; ?>">Retour à la gestion de la promotion</a></p>
<?php endif; ?>