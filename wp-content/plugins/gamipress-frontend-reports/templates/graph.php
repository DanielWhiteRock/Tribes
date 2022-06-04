<?php
/**
 * Graph template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/frontend-reports/graph.php
 * To override a specific rank type just copy it as yourtheme/gamipress/frontend-reports/graph-{type}.php
 */
global $gamipress_frontend_reports_template_args;

// Shorthand
$a = $gamipress_frontend_reports_template_args; ?>

<div id="<?php echo $a['id']; ?>" class="gamipress-frontend-reports-graph"
     data-graph="<?php echo $a['graph']; ?>"
     data-current-user="<?php echo $a['current_user']; ?>"
     data-user-id="<?php echo $a['user_id']; ?>"
     data-grid="<?php echo $a['grid']; ?>"
     data-max-ticks="<?php echo $a['max_ticks']; ?>"
     data-legend="<?php echo $a['legend']; ?>"
     data-background="<?php echo $a['background']; ?>"
     data-border="<?php echo $a['border']; ?>"
>

    <?php
    /**
     * Before render graph
     *
     * @since 1.0.0
     *
     * @param int       $user_id        User ID
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_frontend_reports_before_render_graph', $a['user_id'], $a ); ?>

    <div class="gamipress-frontend-reports-graph-filters">

        <?php // Period ?>
        <?php if( $a['period'] === 'yes' ) : ?>

            <div class="gamipress-frontend-reports-graph-period-wrapper">

                <select id="period" class="gamipress-frontend-reports-graph-period">
                    <?php foreach( gamipress_frontend_reports_get_graph_time_periods() as $period => $label ) : ?>
                        <option value="<?php echo $period; ?>" <?php selected( $period, $a['period_value'] ); ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="date" id="period_start" class="gamipress-frontend-reports-graph-period-start" value="<?php echo $a['period_start']; ?>" <?php if( $a['period_value'] !== 'custom' ) : ?>style="display: none;"<?php endif; ?>>

                <input type="date" id="period_end" class="gamipress-frontend-reports-graph-period-end" value="<?php echo $a['period_end']; ?>" <?php if( $a['period_value'] !== 'custom' ) : ?>style="display: none;"<?php endif; ?>>

            </div>

        <?php else : ?>

            <input type="hidden" id="period" class="gamipress-frontend-reports-graph-period" value="<?php echo $a['period_value']; ?>">
            <input type="hidden" id="period_start" class="gamipress-frontend-reports-graph-period-start" value="<?php echo $a['period_start']; ?>">
            <input type="hidden" id="period_end" class="gamipress-frontend-reports-graph-period-end" value="<?php echo $a['period_end']; ?>">

        <?php endif; ?>

        <?php // Range ?>
        <?php if( $a['range'] === 'yes' ) : ?>

            <div class="gamipress-frontend-reports-graph-range-wrapper">

                <select id="range" class="gamipress-frontend-reports-graph-range" <?php if( $a['period_value'] !== 'custom' ) : ?>style="display: none;"<?php endif; ?>>
                    <?php foreach( gamipress_frontend_reports_get_graph_ranges() as $range => $label ) : ?>
                        <option value="<?php echo $range; ?>" <?php selected( $range, $a['range_value'] ); ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>

            </div>
        <?php else : ?>

            <input type="hidden" id="range" class="gamipress-frontend-reports-graph-range" value="<?php echo $a['range_value']; ?>">

        <?php endif; ?>

        <?php // Filters (extra fields set by each graph needs) ?>
        <?php if( isset( $a['filters'] ) && is_array( $a['filters'] ) ) : ?>

            <?php foreach( $a['filters'] as $filter => $value ) : ?>
                <input type="hidden" id="<?php echo $filter; ?>" class="gamipress-frontend-reports-graph-filter" value="<?php echo $value; ?>">
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <?php // Loading spinner ?>
    <div id="gamipress-frontend-reports-spinner" class="gamipress-spinner" style="display: none;"></div>

    <?php
    /**
     * After render graph
     *
     * @since 1.0.0
     *
     * @param int       $user_id        User ID
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_frontend_reports_after_render_graph', $a['user_id'], $a ); ?>

</div>
