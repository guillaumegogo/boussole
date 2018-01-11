<h1>Contact</h1>

<p>La Boussole s’améliore grâce à vos retours ! Nous prenons en compte toutes les remarques que nous recevons. </p>
<p>Vous avez une idée pour améliorer la Boussole des jeunes,  vous souhaitez nous donner votre avis, nous signaler une erreur : contactez-nous ! </p>
<p>Vous êtes un partenaire potentiel et souhaitez rejoindre le projet, contactez-nous ! </p>

<form id="contact_form" action="#" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="test" value="<?= $test_antispam ?>" />
	
	<div class="row">
		<div class="col-sm-12">
			<label for="subject">Sujet:</label><br />
			<select id="subject" class="input" name="subject_contact" required >
				<?php foreach($sujets_contact as $key=>$val) {?>
				<option value="<?=$key?>"><?=$val?></option>
				<?php }?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="message">Message:</label><br />
			<textarea id="message" class="input" name="message_contact" rows="7" cols="30" required></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="name">Nom:</label><br />
			<input id="name" class="input" name="name_contact" type="text" value="" size="30" required />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="email">Email:</label><br />
			<input id="email" class="input" name="email_contact" type="text" value="" size="30" required />
		</div>
	</div>
	<input id="submit_button" type="submit" value="Envoyer votre demande" name="envoi_mail" />
</form>