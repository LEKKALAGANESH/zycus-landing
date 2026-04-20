<?php
/* Elementor canvas — outputs header/footer around the Elementor-built content */
get_header();
?>
<main id="main-content" role="main">
<?php
while (have_posts()) {
    the_post();
    the_content();
}
?>
</main>
<?php get_footer(); ?>
