<?php get_header(); ?>

<?php if (have_posts()) : ?>

    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <article <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="post-content">
                <?php the_content(); ?>
            </div>
        </article>

    <?php endwhile; ?>

<?php endif; ?>

<?php get_footer(); ?>