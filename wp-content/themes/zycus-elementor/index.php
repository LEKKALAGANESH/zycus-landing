<?php get_header(); ?>
<main id="main-content" role="main">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="container" style="padding-top:48px;padding-bottom:48px;">
      <?php the_content(); ?>
    </div>
  </article>
<?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>
