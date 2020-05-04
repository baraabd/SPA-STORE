<?php

/* 
Template Name: Stores
*/

get_header(); 
?>

<div id="primary" class="content-area">
<main id="main" class="site-main" role="main">
<h1 style="text-align:center;font-weight:bold;">Store Locations</h1>
<?php
 $args = array(  
        'post_type' => 'stores',
        'post_status' => 'publish',
        'posts_per_page' => 8,
        'orderby' => 'title',
        'order' => 'ASC'
 
    );

    $loop = new WP_Query( $args ); 
        
    while ( $loop->have_posts() ) : $loop->the_post(); 
    $name = get_field('name');
    $note = get_field('note');
    $address = get_field('address'); 
    $map = get_field('map'); 
    ?>
   <h3><?php echo $name; ?></h3>
   <p><?php echo $address;  ?></p>
   <p><?php echo $note ; ?></p>
   <p><?php var_dump($map)  ; ?></p>
    <?php
    endwhile;

    wp_reset_postdata();
?>
    </main><!-- #main -->
	</div><!-- #primary -->
<?php




 do_action( 'storefront_sidebar' );
 get_footer();