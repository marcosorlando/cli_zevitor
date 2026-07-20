<!--Appointment One Start -->
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
									<div class='section-title__shape-1'>
										<i class='section-title__circle'></i>
									</div>
								</div>
								<h6 class='section-title__tagline'>Agendar atendimento</h6>
								<div class='section-title__tagline-border'>
									<div class='section-title__shape-2'>
										<i class='section-title__circle'></i>
									</div>
								</div>
							</div>
							<h3 class='section-title__title title-animation'>Marque agora uma consulta
							</h3>
						</div>
						<form class='contact-form-validated appointment-one__form' action='<?= INCLUDE_PATH ?>/assets/inc/sendemail.php'
						      method='post' novalidate='novalidate'>
							<div class='row'>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' name='name' placeholder='Nome completo' required=''>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='email' name='email' placeholder='Endereço de e-mail' required=''>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' name='Phone' placeholder='Número de telefone' required=''>
									</div>
								</div>
								<div class='col-xl-6 col-lg-6 col-md-6'>
									<div class='appointment-one__input-box'>
										<input type='text' placeholder='mm/dd/aaaa' name='date' id='datepicker'>
									</div>
								</div>
								<div class='col-xl-12'>
									<div class='appointment-one__input-box'>
										<div class='select-box'>
											<select class='selectmenu wide'>
												<option selected>Tipo de serviço</option>
												<option>Tipo de serviço 01</option>
												<option>Tipo de serviço 02</option>
												<option>Tipo de serviço 03</option>
												<option>Tipo de serviço 04</option>
												<option>Tipo de serviço 05</option>
											</select>
										</div>
									</div>
								</div>
								<div class='col-xl-12'>
									<div class='appointment-one__btn-box'>
										<button type='submit' class='thm-btn'>Agendar agora<span
													class='icon-next'></span></button>
									</div>
								</div>
							</div>
						</form>
						<div class='result'></div>
					</div>
				</div>
			</div>
			<div class='appointment-one__img'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/appointment-one-img-1.png' alt=''>
			</div>
		</div>
	</div>
</section>
<!--Appointment One End -->
