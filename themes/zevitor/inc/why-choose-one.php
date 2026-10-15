<?php

    use App\Conn\Read;
    use App\Helpers\Check;

?>
<!--Why Choose One Start -->
<section class='why-choose-one'>
	<ul class='why-choose-one__sliding-text-list list-unstyled marquee_mode-1'>
		<li>
			<h2 data-hover='Branding' class='why-choose-one__sliding-text-title'>Por que nos escolher *</h2>
		</li>
		<li>
			<h2 data-hover='Branding' class='why-choose-one__sliding-text-title'>Por que nos escolher *</h2>
		</li>
		<li>
			<h2 data-hover='Branding' class='why-choose-one__sliding-text-title'>Por que nos escolher *</h2>
		</li>
	</ul>
	<div class='why-choose-one__right-bg-color'></div>
	<div class='why-choose-one__right-bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/why-choose-one-right-bg-img.jpg);'></div>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-6'>
				<div class='why-choose-one__left'>
					<div class='section-title text-left sec-title-animation animation-style2'>
						<div class='section-title__tagline-box'>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-1'>
									<i class='section-title__circle'></i>
								</div>
							</div>
							<h6 class='section-title__tagline'>POR QUE NOS ESCOLHER</h6>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-2'>
									<i class='section-title__circle'></i>
								</div>
							</div>
						</div>
						<h3 class='section-title__title title-animation'>Cuidado excepcional com o carro apoiado por
							Experiência, confiança e uma <span>Paixão pela Eficácia.</span>
						</h3>
					</div>
					<p class='why-choose-one__text'>Diagnóstico assertivo, usando a experiência e o que há de mais
						moderno em equipamentos para manutenção automotiva.
						.</p>
					<div class='why-choose-one__points-box'>
						<div class='why-choose-one__points-list-shape-1'></div>
						<ul class='why-choose-one__points-list list-unstyled'>
							<li>
								<div class='icon'>
									<span class='icon-tools'></span>
								</div>
								<p>Treinamento constante
									<br>da equipe</p>
							</li>
							<li>
								<div class='icon'>
									<span class='icon-technician'></span>
								</div>
								<p>Mecânicos profissionais <br>e qualificados</p>
							</li>
							<li>
								<div class='icon'>
									<span class='icon-satisfaction'></span>
								</div>
								<p>Garantia de 100% de satisfação</p>
							</li>
						</ul>
						<ul class='why-choose-one__points-list list-unstyled'>
							<li>
								<div class='icon'>
									<span class='icon-clock-1'></span>
								</div>
								<p>Opções de agendamento flexíveis</p>
							</li>
							<li>
								<div class='icon'>
									<span class='icon-affordable'></span>
								</div>
								<p>Preços acessíveis e transparentes</p>
							</li>
						</ul>
					</div>
					<div class='why-choose-one__author-box'>
                        <?php

                            $Read ??= new Read();
                            $Read->fullRead(
                                'SELECT * FROM ' . DB_USERS . ' WHERE user_level = :lv ORDER BY user_id LIMIT :limit',
                                'lv=8&limit=1'
                            );

                            if ($Read->getResult()) {
                                $user = $Read->getResult()[0];
                                $userThumb = (string)($user['user_thumb'] ?? '');
                                $userCell = (string)($user['user_cell'] ?? '');
                                $userAlt = trim(
                                    ($user['user_name'] ?? '') . ' ' . ($user['user_lastname'] ?? '')
                                    . (!empty($user['user_description']) ? ' - ' . $user['user_description'] : '')
                                );

                                ?>
                                <?php if ('' !== $userThumb): ?>
								<div class='why-choose-one__author-img'>
									<img src='<?= BASE ?>/tim.php?src=uploads/<?= $userThumb ?>&w=70&h=70'
									     alt='<?= Check::safeHtmlChars($userAlt) ?>'>
								</div>
                                <?php endif; ?>
								<div class='why-choose-one__author-content'>
									<p>Precisa de ajuda? Entre em contato com <span>conosco.</span></p>
                                    <?php if ('' !== $userCell): ?>
									<p><a href='<?= Check::whatsMessage(
                                            $userCell,
                                            'Olá! Estou no site agora e quero agendar uma visita.'
                                        ) ?>' target='_blank' rel='noopener'><?= Check::safeHtmlChars($userCell) ?></a></p>
                                    <?php endif; ?>
								</div>

                                <?php
                            }
                        ?>
					</div>
				</div>
			</div>
			<div class='col-xl-6'>
				<div class='why-choose-one__right'>
					<div class='row'>

						<div class='col-xl-6 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='300ms'>
							<div class='why-choose-one__count-single'>
								<div class='why-choose-one__count-icon'>
									<span class='icon-costumer'></span>
								</div>
								<div class='why-choose-one__count-content'>
									<div class='why-choose-one__count-count-box'>
										<h3 class='odometer' data-count='4,000'>00</h3>
										<span>+</span>
									</div>
									<p class='why-choose-one__count-text'>Clientes satisfeitos</p>
								</div>
							</div>
						</div>


						<div class='col-xl-6 col-lg-6 col-md-6 wow fadeInRight' data-wow-delay='200ms'>
							<div class='why-choose-one__count-single'>
								<div class='why-choose-one__count-icon'>
									<span class='icon-tools-1'></span>
								</div>
								<div class='why-choose-one__count-content'>
									<div class='why-choose-one__count-count-box'>
										<h3 class='odometer' data-count='5,0000'>00</h3>
										<span>+</span>
									</div>
									<p class='why-choose-one__count-text'>Veículos reparados</p>
								</div>
							</div>
						</div>

						<div class='col-xl-6 col-lg-6 col-md-6 wow fadeInRight' data-wow-delay='400ms'>
							<div class='why-choose-one__count-single'>
								<div class='why-choose-one__count-icon'>
									<span class='icon-expert-team'></span>
								</div>
								<div class='why-choose-one__count-content'>
									<div class='why-choose-one__count-count-box'>
										<h3 class='odometer' data-count='5'>00</h3>
										<span>+</span>
									</div>
									<p class='why-choose-one__count-text'>Técnicos automotivos experientes</p>
								</div>
							</div>
						</div>

						<div class='col-xl-6 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='100ms'>
							<div class='why-choose-one__count-single'>
								<div class='why-choose-one__count-icon'>
									<span class='icon-trophy'></span>
								</div>
								<div class='why-choose-one__count-content'>
									<div class='why-choose-one__count-count-box'>
										<h3 class='odometer' data-count='6'>00</h3>
										<span>X</span>
									</div>
									<p class='why-choose-one__count-text'>Campeão Gaúcho de Automobilismo</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Why Choose One End -->
