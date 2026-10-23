<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Time (home) — tema Zevitor.
     * Dinâmico: funcionários ativos de DB_TEAM (user_status = 1), na ordem definida no painel (user_order).
     * Renderiza só se houver registros. O botão sobre a foto mostra o ícone (user_icon) do funcionário.
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT user_fullname, user_skill, user_icon, user_image FROM ' . DB_TEAM
        . ' WHERE user_status = :st AND user_fullname IS NOT NULL ORDER BY user_order ASC, user_fullname ASC',
        'st=1'
    );
    $Team = $Read->getResult() ?: [];

    if ($Team):
        ?>
		<!--Team One Start -->
		<section class='team-one'>
			<div class='team-one__shape-1 float-bob-x'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/team-one-shape-1.png' alt=''>
			</div>
			<div class='container'>
				<div class='section-title text-center sec-title-animation animation-style1'>
					<div class='section-title__tagline-box'>
						<div class='section-title__tagline-border'>
							<div class='section-title__shape-1'>
								<i class='section-title__circle'></i>
							</div>
						</div>
						<h6 class='section-title__tagline'>Equipe Mecânica Zé Vitor</h6>
						<div class='section-title__tagline-border'>
							<div class='section-title__shape-2'>
								<i class='section-title__circle'></i>
							</div>
						</div>
					</div>
					<h3 class='section-title__title title-animation'>Conheça os especialistas que mantêm seu <br>carro
						<span>em movimento com segurança e performance</span>
					</h3>
				</div>
				<div class='team-one__inner'>
					<div class='row'>
                        <?php
                            $TeamTotal = count($Team);
                            foreach ($Team as $Index => $Member):
                                $MemberImage = $Member['user_image'] && file_exists('uploads/' . $Member['user_image'])
                                    ? 'uploads/' . $Member['user_image']
                                    : 'admin/_img/no_avatar.jpg';
                                $MemberIcon = $Member['user_icon'] ?: 'icon-mechanical';
                                $MemberAnimation = $Index < $TeamTotal / 2 ? 'fadeInLeft' : 'fadeInRight';
                                $MemberDelay = (($Index % 4) + 1) * 100;
                                ?>
								<!--Team One Single Start-->
								<div class='col-xl-3 col-lg-6 col-md-6 wow <?= $MemberAnimation ?>'
								     data-wow-delay='<?= $MemberDelay ?>ms'>
									<div class='team-one__single'>
										<div class='team-one__img-box'>
											<div class='team-one__img'>
												<img src='<?= BASE ?>/tim.php?src=<?= $MemberImage ?>&w=300&h=280'
												     alt='<?= Check::safeHtmlChars($Member['user_fullname']) ?>'>
											</div>
											<div class='team-one__social-box'>
												<div class='team-one__plus'>
									<span class='<?= Check::safeHtmlChars($MemberIcon) ?>'
									      title='<?= Check::safeHtmlChars((string)$Member['user_skill']) ?>'></span>
												</div>
											</div>
										</div>
										<div class='team-one__content'>
											<h3 class='team-one__title'><?= Check::safeHtmlChars(
                                                    $Member['user_fullname']
                                                ) ?></h3>
											<p class='team-one__sub-title'><?= Check::safeHtmlChars(
                                                    (string)$Member['user_skill']
                                                ) ?></p>
										</div>
									</div>
								</div>
								<!--Team One Single End-->
                            <?php
                            endforeach;
                        ?>
					</div>
				</div>
			</div>
		</section>
		<!--Team One End -->
    <?php
    endif;
