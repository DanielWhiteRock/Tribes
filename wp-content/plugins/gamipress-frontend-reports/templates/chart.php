<?php
/**
 * Chart template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/frontend-reports/chart.php
 * To override a specific rank type just copy it as yourtheme/gamipress/frontend-reports/chart-{type}.php
 */
global $gamipress_frontend_reports_template_args;

// Shorthand
$a = $gamipress_frontend_reports_template_args; ?>

<div class="gamipress-frontend-reports-chart gamipress-frontend-reports-chart-style-<?php echo $a['style']; ?>"
     data-style="<?php echo $a['style']; ?>"
     data-label="<?php echo $a['label']; ?>"
     data-grid="<?php echo $a['grid']; ?>"
     data-max-ticks="<?php echo $a['max_ticks']; ?>"
     data-legend="<?php echo $a['legend']; ?>"
>

    <?php
    /**
     * Before render chart stats
     *
     * @since 1.0.0
     *
     * @param int       $user_id        User ID
     * @param array     $stats          User stats
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_frontend_reports_before_render_stats', $a['user_id'], $a['stats'], $a ); ?>

    <?php foreach( $a['stats'] as $stat ) : ?>

        <div class="gamipress-frontend-reports-chart-stat gamipress-frontend-reports-chart-stat-<?php echo $stat['slug']; ?>"
             data-background="<?php echo $stat['background']; ?>"
             data-border="<?php echo $stat['border']; ?>"
        >

            <?php
            /**
             * Before render a chart stat
             *
             * @since 1.0.0
             *
             * @param int       $user_id        User ID
             * @param array     $stat           Stat that is rendered. Keys: name, slug, value, background, border
             * @param array     $stats          User stats
             * @param array     $template_args  Template received arguments
             */
            do_action( 'gamipress_frontend_reports_before_render_stat', $a['user_id'], $stat, $a['stats'], $a ); ?>

            <div class="gamipress-frontend-reports-chart-stat-name gamipress-frontend-reports-chart-stat-<?php echo $stat['slug']; ?>-name"><?php echo $stat['name']; ?></div>

            <div class="gamipress-frontend-reports-chart-stat-value gamipress-frontend-reports-chart-stat-<?php echo $stat['slug']; ?>-value"><?php echo $stat['value']; ?></div>

            <?php
            /**
             * After render a chart stat
             *
             * @since 1.0.0
             *
             * @param int       $user_id        User ID
             * @param array     $stat           Stat that is rendered. Keys: name, slug, value, background, border
             * @param array     $stats          User stats
             * @param array     $template_args  Template received arguments
             */
            do_action( 'gamipress_frontend_reports_after_render_chart_stat', $a['user_id'], $stat, $a['stats'], $a ); ?>

        </div>

    <?php endforeach; ?>

    <?php
    /**
     * After render chart stats
     *
     * @since 1.0.0
     *
     * @param int       $user_id        User ID
     * @param array     $stats          User stats
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_frontend_reports_after_render_stats', $a['user_id'], $a['stats'], $a ); ?>

</div>
