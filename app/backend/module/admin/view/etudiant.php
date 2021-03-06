<?php if ($showProfil) : ?>
   <h1>Profil étudiant</h1>
   <p><a href="/admin/utilisateurs/<?php echo $etudiant['idUtil']; ?>/modifier" class="button orangeButton">Modifier ce profil</a> <a href="/admin/exporter/étudiant/<?php echo $etudiant['idUtil']; ?>" class="button blueButton">Exporter ce profil</a></p>
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
            <li><a href="/admin/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/matières"><?php echo $module['libelle']; ?></a>
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
                     <li><a href="/admin/<?php echo $etudiant['promo']; ?>/<?php echo $module['libelle']; ?>/<?php echo $matiere['libelle']; ?>"><?php echo $matiere['libelle']; ?></a>
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
   <p><a href="/admin/<?php echo $etudiant['promo']; ?>/étudiants">Retour à la liste des étudiants</a></p>
   <?php
else :
   ?>
   <h1>Liste des étudiants <?php echo $prefixPromo . $promo; ?></h1>
   <p>Pour ajouter un étudiant, veuillez cliquer <a href="/admin/utilisateurs">ici</a>.</p>
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
                  <td><a href="/admin/étudiant/<?php echo $etudiant['idUtil']; ?>/profil"><img src="/img/admin/go_user.png" alt="Profil élève" title="Consulter le profil de cet élève" /></a> <a href="/admin/utilisateurs/<?php echo $etudiant['idUtil']; ?>/modifier"><img src="/img/admin/user_edit.png" alt="Modifier cet élève" title="Modifier cet élève" /></a> <a href="/admin/utilisateurs/<?php echo $etudiant['idUtil']; ?>/supprimer"><img src="/img/admin/user_delete.png" alt="Supprimer cet élève" title="Supprimer cet élève" /></a></td>
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
   <p><a href="/admin/<?php echo $promo ?>">Retour à la promotion</a></p>
<?php endif; ?>