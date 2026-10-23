<?php

use App\Conn\Read;
use App\Helpers\Check;

$Read ??= new Read();
$Read->fullRead(
    'SELECT svc_title FROM ' . DB_SERVICES . ' WHERE svc_status = :status ORDER BY svc_title ASC',
    'status=1'
);
$AppointmentServices = $Read->getResult() ?: [];
?>

<section class='appointment-one'>
	<div class='container'>
		<div class='appointment-one__inner'>
			<div class='appointment-one__bg-shape'
			     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/appointment-one-bg-shape.png);'></div>
			<div class='row'>
				<div class='col-xl-5 col-lg-4'></div>
				<div class='col-xl-7 col-lg-8'>
					<div class='appointment-one__right'>
						<div class='section-title text-left sec-title-animation animation-style2'>
							<div class='section-title__tagline-box'>
								<div class='section-title__tagline-border'>
									<div class='section-title__shape-1'><i class='section-title__circle'></i></div>
								</div>
								<h6 class='section-title__tagline'>Agendar atendimento</h6>
								<div class='section-title__tagline-border'>
									<div class='section-title__shape-2'><i class='section-title__circle'></i></div>
								</div>
							</div>
							<h3 class='section-title__title title-animation'>Solicite seu atendimento</h3>
						</div>
						<form class='contact-form-validated appointment-one__form' action='<?= BASE ?>/contato' method='post'>
							<input type='hidden' name='action' value='contact'>
							<div class='row'>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' name='nome' placeholder='Seu nome*' required>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='email' name='email' placeholder='Seu e-mail*' required>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' class='formPhone' name='telefone' placeholder='WhatsApp*' required>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' name='assunto' placeholder='Assunto*' required>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<select class='ignore' name='servico' required>
											<option value='' selected disabled>Selecione um serviço*</option>
											<?php foreach ($AppointmentServices as $Service): ?>
												<?php $ServiceTitle = Check::safeHtmlChars($Service['svc_title']); ?>
												<option value='<?= $ServiceTitle ?>'><?= $ServiceTitle ?></option>
											<?php endforeach; ?>
											<option value='Outro'>Outro</option>
										</select>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' name='veiculo' placeholder='Marca / Modelo / Ano do carro*' required>
									</div>
								</div>
								<div class='col-xl-12'>
									<div class='appointment-one__input-box'>
										<textarea name='mensagem' placeholder='Sua mensagem, descreva o que está ocorrendo com seu veículo*' required></textarea>
									</div>
								</div>
								<div class='col-xl-12'>
									<div class='appointment-one__btn-box'>
										<button type='submit' class='thm-btn'>Enviar solicitação<span class='icon-next'></span></button>
									</div>
								</div>
							</div>
							<div class='result' role='status' aria-live='polite'></div>
						</form>
					</div>
				</div>
			</div>
			<div class='appointment-one__img'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/appointment-one-img-1.png' alt=''>
			</div>
		</div>
	</div>
</section>
<script src='<?= BASE ?>/assets/js/text.control.min.js'></script>
