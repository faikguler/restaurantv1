<?php
/**
 * Template Name: Menu Page
 */
get_header();
?>

<div class="page-content menu-page">
        <?php echo do_shortcode('[restaurant_menu_section]'); ?>
</div>

<?php get_footer(); ?>