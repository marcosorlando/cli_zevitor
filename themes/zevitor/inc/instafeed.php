<?php

use App\Helpers\Check;
use App\Helpers\Instagram;
use App\Helpers\InstagramToken;

$InstagramToken = trim((string) ($_ENV['INSTAGRAM_ACCESS_TOKEN'] ?? ''));
if ($InstagramToken === '') {
    $InstagramToken = trim((string) (getenv('INSTAGRAM_ACCESS_TOKEN') ?: ''));
}
$InstagramVersion = trim((string) ($_ENV['INSTAGRAM_API_VERSION'] ?? 'v25.0'));
$InstagramToken = (new InstagramToken($InstagramToken))->get();
$InstagramPosts = (new Instagram($InstagramToken, $InstagramVersion))->getRecent(9);
$InstagramUsername = defined('SITE_SOCIAL_INSTAGRAM') ? trim((string) SITE_SOCIAL_INSTAGRAM, '@') : '';
$InstagramProfile = preg_match('/^[A-Za-z0-9._]+$/', $InstagramUsername)
    ? 'https://www.instagram.com/' . $InstagramUsername . '/'
    : 'https://www.instagram.com/';

if ($InstagramPosts):
?>
<section class='gallery-one'>
	<div class='container'>
		<div class='gallery-one__top-title-box'>
			<p class='gallery-one__top-title'>
				<a href='<?= Check::safeHtmlChars($InstagramProfile) ?>' target='_blank' rel='noopener noreferrer'>Siga o Instagram</a>
			</p>
		</div>
	</div>
	<div class='gallery-one__carousel swiper-container'>
		<div class='swiper-wrapper'>
			<?php foreach ($InstagramPosts as $Post): ?>
				<div class='swiper-slide'>
					<div class='gallery-one__single'>
						<div class='gallery-one__img'>
							<img src='<?= Check::safeHtmlChars($Post['image']) ?>'
							     alt='Publicação da Mecânica Zé Vitor no Instagram' loading='lazy'>
							<a href='<?= Check::safeHtmlChars($Post['link']) ?>' target='_blank'
							   rel='noopener noreferrer' aria-label='Ver publicação no Instagram'>
								<span class='fab fa-instagram' aria-hidden='true'></span>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
