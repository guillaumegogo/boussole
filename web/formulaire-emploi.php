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
				<select name="age" class="age">
					<option value="16">16</option><option value="17">17</option><option value="18" selected >18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option>
				</select> ans
			</div>

			<div class="lab">
				<label class="label_long" for="europeen">J'ai la nationalité d'un pays de l'Union européenne :</label>
				<input type="radio" name="europeen" value="oui"> Oui
				<input type="radio" name="europeen" value="non"> Non<br>
			</div>
			
			<div class="lab">
				<label class="label_long" for="sexe">Je suis :</label>
				<input type="radio" name="sexe" value="h"> Un homme
				<input type="radio" name="sexe" value="f"> Une femme
			</div>

			<div class="lab">
				<label class="label_long" for="jesais">Je sais ce que je veux faire :</label>
				<input type="radio" name="jesais" value="oui"> Oui
				<input type="radio" name="jesais" value="non"> Non<br>
			</div>
			
			<div style="margin-top:2em;"><button type="submit" style="float:right" >Je continue</button></div>
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
				<label class="label_long" for="situation">Quelle est ma situation aujourd'hui  ?</label>
				<select name="situation">
					<option value="sans activite" >Sans activité</option>
					<option value="collegien" >Collégien</option>
					<option value="lyceen">Lycéen</option>
					<option value="etudiant">Etudiant</option>
					<option value="stagiaire form pro">Stagiaire form pro</option>
					<option value="apprenti">Apprenti</option>
					<option value="salarie">Salarié</option>
					<option value="independant">Indépendant</option>
					<option value="auto entrepreneur">Auto entrepreneur</option>
					<option value="autre">Autre</option>
				</select>
			</div>

			<div class="lab">
				<label class="label_long" for="etudes">Quelles sont les dernières études que j'ai suivies ?</label>
				<select name="etudes">
					<option value="college" >Collège</option>
					<option value="lycee" >Lycée</option>
					<option value="etudes superieures" >Etudes supérieures</option>
					<option value="aprentissage" >Apprentissage</option>
					<option value="formation professionnelle" >Formation professionnelle</option>
					<option value="etranger" >Etudes à l'étranger</option>
				</select>
			</div>

			<div class="lab">
				<label class="label_long" for="diplome">Quel est le diplôme le plus élevé que j'ai obtenu ?</label>
				<select name="diplome">
					<option value="brevet" >Brevet des collèges</option>
					<option value="cap" >CAP</option>
					<option value="bep" >BEP</option>
					<option value="bac general" >Baccalauréat général</option>
					<option value="bac pro">Baccalauréat professionnel</option>
					<option value="bts dut" >BTS / DUT</option>
					<option value="licence">Licence</option>
					<option value="master">Master</option>
					<option value="doctorat" >Doctorat</option>
					<option value="etranger" >Diplôme étranger</option>
				</select>
			</div>

			<div class="lab">
				<label class="label_long" for="permis">Ai-je le permis de conduire (B) ?</label>
				<input type="radio" name="permis" value="oui"> Oui
				<input type="radio" name="permis" value="non"> Non
			</div>

			<div class="lab">
				<label class="label_long" for="handicap">Suis-je en situation de handicap ?</label>
				<input type="radio" name="handicap" value="oui"> Oui
				<input type="radio" name="handicap" value="non"> Non
				<input type="radio" name="handicap" value=""> Je ne sais pas
			</div>
		
			<div style="margin-top:2em;"><input type="button" value="Retour" onclick="history.go(-1)" style="float:left"> <button type="submit" style="float:right">Je continue</button></div>
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
				<div style="display:inline-table;">
					<input type="checkbox" name="type_emploi[]" value="job ete"> Job d'été ou saisonnier<br/>
					<input type="checkbox" name="type_emploi[]" value="job etudiant" > Job étudiant<br/>
					<input type="checkbox" name="type_emploi[]" value="job ete" > Emploi "durable"<br/>
					<input type="checkbox" name="type_emploi[]" value="job etudiant" > Emploi avec une formation
				</div>
			</div>

			<div class="lab">
				<label class="label_long" for="temps_plein">Je souhaite un travail à temps plein :</label>
				<select name="temps_plein">
					<option value="oui" >Oui</option>
					<option value="non" >Non, à temps partiel</option>
					<option value="flexible" >Je suis flexible</option>
				</select>
			</div>

			<div class="lab">
				<label class="label_long" for="secteur[]">Quel secteur d'activité ou métier m'intéresse ?<br/><small>(Choix multiple possible)</small></label>
				<select name="secteur[]" multiple size=6>
				<option value="Agriculture" >Agriculture</option>
				<option value="Agroalimentaire - Alimentation" >Agroalimentaire - Alimentation</option>
				<option value="Animaux" >Animaux</option>
				<option value="Architecture - Aménagement intérieur" >Architecture - Aménagement intérieur</option>
				<option value="Artisanat - Métiers d\'art" >Artisanat - Métiers d'art</option>
				<option value="Banque - Finance - Assurance" >Banque - Finance - Assurance</option>
				<option value="Bâtiment - Travaux publics" >Bâtiment - Travaux publics</option>
				<option value="Biologie - Chimie" >Biologie - Chimie</option>
				<option value="Commerce - Immobilier" >Commerce - Immobilier</option>
				<option value="Communication - Information" >Communication - Information</option>
				<option value="Culture - Spectacle" >Culture - Spectacle</option>
				<option value="Défense - Sécurité - Secours" >Défense - Sécurité - Secours</option>
				<option value="Droit" >Droit</option>
				<option value="Edition - Imprimerie - Livre" >Edition - Imprimerie - Livre</option>
				<option value="Electronique - Informatique" >Electronique - Informatique</option>
				<option value="Enseignement - Formation" >Enseignement - Formation</option>
				<option value="Environnement - Nature - Nettoyage" >Environnement - Nature - Nettoyage</option>
				<option value="Gestion - Audit - Ressources humaines" >Gestion - Audit - Ressources humaines</option>
				<option value="Hôtellerie - Restauration - Tourisme" >Hôtellerie - Restauration - Tourisme</option>
				<option value="Humanitaire" >Humanitaire</option>
				<option value="Industrie - Matériaux" >Industrie - Matériaux</option>
				<option value="Lettres - Sciences humaines" >Lettres - Sciences humaines</option>
				<option value="Mécanique - Maintenance" >Mécanique - Maintenance</option>
				<option value="Numérique - Multimédia - Audiovisuel" >Numérique - Multimédia - Audiovisuel</option>
				<option value="Santé" >Santé</option>
				<option value="Sciences - Maths - Physique" >Sciences - Maths - Physique</option>
				<option value="Secrétariat - Accueil" >Secrétariat - Accueil</option>
				<option value="Social - Services à la personne" >Social - Services à la personne</option>
				<option value="Soins - Esthétique - Coiffure" >Soins - Esthétique - Coiffure</option>
				<option value="Sport - Animation" >Sport - Animation</option>
				<option value="Transport - Logistique" >Transport - Logistique</option>
				</select>
			</div>
			
			<div class="lab">
				<label class="label_long" for="experience">J'ai déjà une expérience en emploi :</label>
				<input type="radio" name="experience" value="oui"> Oui
				<input type="radio" name="experience" value="non"> Non
			</div>

			<div class="lab">
				<label class="label_long" for="inscription[">Je suis déjà inscrit auprès de :</label>
				<div style="display:inline-table;">
					<input type="checkbox" name="inscription[]" value="pole emploi" > Pôle emploi<br/>
					<input type="checkbox" name="inscription[]" value="cap emploi"  > Cap emploi<br/>
					<input type="checkbox" name="inscription[]" value="mission locale"  > Mission locale<br/>
					<input type="checkbox" name="inscription[]" value="apec"  > APEC
				</div>
			</div>		
		
			<div style="margin-top:2em;"><input type="button" value="Retour" onclick="history.go(-1)" style="float:left"> <button type="submit" style="float:right">Je continue</button></div>
		</div>
	</fieldset>
	
<?php
}
?>
</form>