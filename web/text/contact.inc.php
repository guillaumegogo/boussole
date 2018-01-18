<h1>Contactez-nous</h1>

<p>La Boussole s&rsquo;am&eacute;liore gr&acirc;ce &agrave; vos retours&nbsp;! Nous prenons en compte toutes les remarques que nous recevons.&nbsp;</p>
<p>Vous avez une id&eacute;e pour am&eacute;liorer la Boussole des jeunes,&nbsp; vous souhaitez nous donner votre avis, nous signaler une erreur&nbsp;: <u>contactez-nous</u> !</p>
<p>Vous &ecirc;tes un partenaire potentiel, un professionnel et souhaitez rejoindre le projet&nbsp;: <u>contactez-nous</u> !</p>
<p>&nbsp;</p>

<form id="contact_form" action="#" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="test" value="<?= $test_antispam ?>" />
	
	<div class="row">
		<div class="col-sm-12">
			<label for="subject">Sujet:</label>
			<select id="subject" class="input" name="subject_contact" required >
				<?php foreach($sujets_contact as $key=>$val) {?>
				<option value="<?=$key?>"><?=$val?></option>
				<?php }?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="message">Message:</label>
			<textarea id="message" class="input" name="message_contact" rows="7" cols="30" required></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="name">Nom:</label>
			<input id="name" class="input" name="name_contact" type="text" value="" size="30" required />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label for="email">Email:</label>
			<input id="email" class="input" name="email_contact" type="text" value="" size="30" required />
		</div>
	</div>
	<input id="submit_button" type="submit" value="Envoyer votre demande" name="envoi_mail" />
</form>