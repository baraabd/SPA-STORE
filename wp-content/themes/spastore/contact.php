<?php

/* 
Template Name: Contact
*/



get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<?php 

$form1 = get_field( 'contact' ); 
echo  $form1;
 ?>
		</main><!-- #main -->
	</div><!-- #primary -->

 <?php

do_action( 'storefront_sidebar' );
get_footer();