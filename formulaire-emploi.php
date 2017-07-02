<?php
//********* description du formulaire
$tab=array();
$tab["age"] = array("type" => "age", "min" => 16, "max" => 30, "defaut" => 18);
$tab["nationalite"] = array("type" => "radio", "francais" => "français", "europeen" => "européen", "hors-ue" => "hors-UE");
$tab["sexe"] = array("type" => "radio", "f" => "une femme", "h" => "un homme");
$tab["jesais"] = array("type" => "radio", "oui" => "Oui", "non" => "Non");

$tab["situation"] = array("type" => "select", "sans activite" => "Sans activité",  "collegien" => "Collégien",  "lyceen" => "Lycéen",  "etudiant" => "Etudiant",  "stagiaire form pro" => "Stagiaire form pro",  "apprenti" => "Apprenti",  "salarie" => "Salarié",  "independant" => "Indépendant",  "auto entrepreneur" => "Auto entrepreneur",  "autre" => "Autre");
$tab["etudes"] = array("type" => "select", "aucune" => "Aucune", "college" => "Collège", "lycee" => "Lycée", "etudes superieures" => "Etudes supérieures", "aprentissage" => "Apprentissage", "formation professionnelle" => "Formation professionnelle", "etranger" => "Etudes à l'étranger");
$tab["diplome"] = array("type" => "select", "aucun" => "Aucun", "brevet" => "Brevet des collèges", "cap" => "CAP", "bep" => "BEP", "bac general" => "Baccalauréat général", "bac pro" =>"Baccalauréat professionnel", "bts dut" => "BTS / DUT", "licence" =>"Licence", "master" =>"Master", "doctorat" => "Doctorat", "etranger" => "Diplôme étranger");
$tab["permis"] = array("type" => "radio", "oui" => "Oui", "non" => "Non");
$tab["handicap"] = array("type" => "radio", "oui" => "Oui", "non" => "Non", "" => "Je ne sais pas");

$tab["type_emploi"] = array("type" => "checkbox", "ete" => "Job d'été ou saisonnier", "etudiant" => "Job étudiant", "durable" => "Emploi \"durable\"", "formation" => "Emploi avec une formation");
$tab["temps_plein"] = array("type" => "select", "oui" => "Oui", "non" => "Non, à temps partiel", "" => "Je suis flexible");
$tab["secteur"] = array("type" => "multiple", "size"=>6, "Agriculture" => "Agriculture","Agroalimentaire - Alimentation" => "Agroalimentaire - Alimentation","Animaux" => "Animaux","Architecture - Aménagement intérieur" => "Architecture - Aménagement intérieur","Artisanat - Métiers d\'art" => "Artisanat - Métiers d'art","Banque - Finance - Assurance" => "Banque - Finance - Assurance","Bâtiment - Travaux publics" => "Bâtiment - Travaux publics","Biologie - Chimie" => "Biologie - Chimie","Commerce - Immobilier" => "Commerce - Immobilier","Communication - Information" => "Communication - Information","Culture - Spectacle" => "Culture - Spectacle","Défense - Sécurité - Secours" => "Défense - Sécurité - Secours","Droit" => "Droit","Edition - Imprimerie - Livre" => "Edition - Imprimerie - Livre","Electronique - Informatique" => "Electronique - Informatique","Enseignement - Formation" => "Enseignement - Formation","Environnement - Nature - Nettoyage" => "Environnement - Nature - Nettoyage","Gestion - Audit - Ressources humaines" => "Gestion - Audit - Ressources humaines","Hôtellerie - Restauration - Tourisme" => "Hôtellerie - Restauration - Tourisme","Humanitaire" => "Humanitaire","Industrie - Matériaux" => "Industrie - Matériaux","Lettres - Sciences humaines" => "Lettres - Sciences humaines","Mécanique - Maintenance" => "Mécanique - Maintenance","Numérique - Multimédia - Audiovisuel" => "Numérique - Multimédia - Audiovisuel","Santé" => "Santé","Sciences - Maths - Physique" => "Sciences - Maths - Physique","Secrétariat - Accueil" => "Secrétariat - Accueil","Social - Services à la personne" => "Social - Services à la personne","Soins - Esthétique - Coiffure" => "Soins - Esthétique - Coiffure","Sport - Animation" => "Sport - Animation","Transport - Logistique" => "Transport - Logistique");
$tab["experience"] = array("type" => "radio", "oui" => "Oui", "non" => "Non");
$tab["inscription"] = array("type" => "checkbox", "pole emploi" => "Pôle emploi", "cap emploi" => "Cap emploi", "mission locale" => "Mission locale", "apec" => "APEC (cadres)");
?>

<form action="formulaire.php" method="post" class="joli formulaire">

<?php
if ($etape=="1") {
?>
	
	<fieldset class="formulaire">
		<legend>Mon profil (1/3)</legend>
		<div class="aide"><img src="img/ci_help.png" title="Ici des explications, la liste des pays de l'UE ou que sais-je."></div>

		<div class="centreformulaire">
			<input type="hidden" name="etape" value="2">
			
			<div class="lab">
				<label class="label_long" for="age">Je suis âgé·e de :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("age", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="nationalite">Je suis citoyen :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("nationalite", $tab); ?></div>
			</div>
			
			<div class="lab">
				<label class="label_long" for="sexe">Je suis :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("sexe", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="jesais">Je sais ce que je veux faire :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("jesais", $tab); ?></div>
			</div>
			
			<div style="margin-top:2em;"><button type="submit" style="float:right">Je continue</button></div>
		</div>
		
	</fieldset>
	
<?php
}else if ($etape=="2") {
?>	
	<fieldset class="formulaire">
		<legend>Ma situation (2/3)</legend>
		<div class="aide"><img src="img/ci_help.png" title="Ici des explications."></div>

		<div class="centreformulaire">
			<input type="hidden" name="etape"  value="3">
			
			<div class="lab">
				<label class="label_long" for="situation">Quelle est ma situation aujourd'hui ?</label>
				<div style="display:inline-block; padding:0;"><?php echo affiche_formulaire("situation", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="etudes">Quelles sont les dernières études que j'ai suivies ?</label>
				<div style="display:inline-block; padding:0;"><?php echo affiche_formulaire("etudes", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="diplome">Quel est le diplôme le plus élevé que j'ai obtenu ?</label>
				<div style="display:inline-block; padding:0;"><?php echo affiche_formulaire("diplome", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="permis">Ai-je le permis de conduire (B) ?</label>
				<div style="display:inline-block; padding:0;"><?php echo affiche_formulaire("permis", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="handicap">Suis-je en situation de handicap ?</label>
				<div style="display:inline-block; padding:0;"><?php echo affiche_formulaire("handicap", $tab); ?></div>
			</div>
		
			<div style="margin-top:2em;">
				<input type="button" value="Retour" onclick="history.go(-1)" style="float:left"> 
				<button type="submit" style="float:right">Je continue</button>
			</div>
		</div>
	</fieldset>
	
<?php
}else if ($etape=="3") {
?>
		
	<fieldset class="formulaire">
		<legend>Ma situation (3/3)</legend>
		<div class="aide"><img src="img/ci_help.png" title="Ici des explications."></div>

		<div class="centreformulaire">
			<input type="hidden" name="etape" value="fin">
			<div class="lab">
				<label class="label_long" for="type_emploi[]">Je cherche un emploi de type :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("type_emploi", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="temps_plein">Je souhaite un travail à temps plein :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("temps_plein", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="secteur[]">Quel secteur d'activité ou métier m'intéresse ?<br/><span style="font-size:0.7em">(Choix multiple possible)</span></label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("secteur", $tab); ?></div>
			</div>
			
			<div class="lab">
				<label class="label_long" for="experience">J'ai déjà une expérience en emploi :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("experience", $tab); ?></div>
			</div>

			<div class="lab">
				<label class="label_long" for="inscription[]">Je suis déjà inscrit auprès de :</label>
				<div style="display:inline-block;"><?php echo affiche_formulaire("inscription", $tab); ?></div>
			</div>		
		
			<div style="margin-top:2em;">
				<input type="button" value="Retour" onclick="history.go(-1)" style="float:left"> 
				<button type="submit" style="float:right">Je continue</button>
			</div>
		</div>
	</fieldset>
	
<?php
}
?>
</form>