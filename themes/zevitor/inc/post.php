<?php

    use App\Helpers\DateHelper;

?>
<!--Blog Two Single Start-->
<div class='col-xl-6 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='100ms'>
	<div class='blog-two__single'>
		<div class='blog-two__single-inner'>
			<div class='blog-two__img-box'>
				<div class='blog-two__img'>
					<img src='<?= BASE ?>/tim.php?src=uploads/<?= $post_cover ?>&w=1200&h=628' alt='<?= $post_title ?>'>
					<div class='blog-two__plus'>
						<a href='blog-details.html'><i class='fas fa-plus'></i></a>
					</div>
					<div class='blog-two__tag'>
						<a href='<?= BASE ?>/artigo/<?= $post_name ?>'><?= $post_title ?></a>
					</div>
				</div>
				<div class='blog-two__date'>
					<p>25 <span>Mar</span></p>
				</div>
			</div>
			<div class='blog-two__content'>
				<ul class='blog-two__meta list-unstyled'>
					<li>
						<a href='blog-details.html'>
							<span class='fas fa-calendar-alt'></span><?= DateHelper::human($post_date) ?></a>
					</li>
					<li>
						<a href='blog-details.html'>
							<span class='fas fa-comments'></span>Comentário
						</a>
					</li>
				</ul>
				<h3 class='blog-two__title'><a href='<?= BASE ?>/artigo/<?= $post_name ?>'><?= $post_title ?></a></h3>
			</div>
		</div>
		<div class='blog-two__author-info'>
			<div class='blog-two__author-img-box'>
				<div class='blog-two__author-img'>
					<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-two-author-img-1.jpg'
					     alt=''>
				</div>
			</div>
			<div class='blog-two__author-content'>
				<h5><?= $authorName ?></h5>
				<p><?= DateHelper::human($post_date) ?></p>
			</div>
		</div>
		<div class='blog-two__read-more'>
			<a href='<?= BASE ?>/artigo/<?= $post_name ?>'><span class='icon-next'></span>Leia mais</a>
		</div>
	</div>
</div>
<!--Blog Two Single End-->
