<?php
/*
Plugin Name: TablePress Extension: Chartist
Plugin URI: https://github.com/silsha/tablepress_chartist
Description: Extension for TablePress to create a responsive chart based on the data in a TablePress table.
Version: 0.10.1
Author: Silsha Fux
Author URI: https://silsha.me
License: GPL
*/

// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

/*
 * Initialize the TablePress Chartist Extension.
 */
add_action('tablepress_run', ['TablePress_Chartist', 'init']);

/**
 * Class that contains the TablePress Chartist Extension functionality.
 *
 * @author Per Soderlind, Tobias Bäthge
 *
 * @since 0.1
 */
class TablePress_Chartist
{
    /**
     * Version number of the Extension.
     *
     * @since 0.1
     *
     * @var string
     */
    protected static $version = '0.10.1';

    /**
     * Available Shortcode attributes, without the `chartist_` prefix.
     *
     * @since 0.2
     *
     * @var array
     */
    protected static $shortcode_attributes = [
        'low'              => null,
        'high'             => null,
        'width'            => '',
        'height'           => '',
        'chart'            => 'line',
        'showline'         => true,
        'showarea'         => false,
        'showpoint'        => true,
        'linesmooth'       => true,
        'aspect_ratio'     => '3:4',
        'horizontal'       => false,
        'stack'            => false,
        'animation'        => false,
        'label_offset'     => false,
        'chart_padding'    => false,
        'donut_width'      => false,
    ];

    /**
     * Mapping of some Shortcode parameters to their ChartistJS equivalent.
     *
     * @since 0.2
     *
     * @var array
     */
    protected static $attribute_to_js_mapping = [
        'low'              => 'low',
        'high'             => 'high',
        'showline'         => 'showLine',
        'showarea'         => 'showArea',
        'showpoint'        => 'showPoint',
        'linesmooth'       => 'lineSmooth',
        'horizontal'       => 'horizontalBars',
        'stack'            => 'stackBars',
        'label_offset'     => 'labelOffset',
        'chart_padding'    => 'chartPadding',
        'donut_width'      => 'donutWidth',
    ];

    /**
     * Available aspect ratios for the chart.
     *
     * @since 0.2
     *
     * @var array
     */
    protected static $aspect_ratios = [
        '1'       => 'ct-square',
        '15:16'   => 'ct-minor-second',
        '8:9'     => 'ct-major-second',
        '5:6'     => 'ct-minor-third',
        '4:5'     => 'ct-major-third',
        '3:4'     => 'ct-perfect-fourth',
        '2:3'     => 'ct-perfect-fifth',
        '5:8'     => 'ct-minor-sixth',
        '1:1.618' => 'ct-golden-section',
        '3:5'     => 'ct-major-sixth',
        '9:16'    => 'ct-minor-seventh',
        '8:15'    => 'ct-major-seventh',
        '1:2'     => 'ct-octave',
        '2:5'     => 'ct-major-tenth',
        '3:8'     => 'ct-major-eleventh',
        '1:3'     => 'ct-major-twelfth',
        '1:4'     => 'ct-double-octave',
    ];

    /**
     * Register necessary plugin filter hooks and the [table-chart] Shortcode.
     *
     * @since 0.1
     */
    public static function init()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts_styles']);
        add_filter('tablepress_shortcode_table_default_shortcode_atts', [__CLASS__, 'register_shortcode_attributes']);
        add_filter('tablepress_table_output', [__CLASS__, 'generate_chart'], 10, 3);
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            // TablePress doesn't support the REST API
        } else {
            add_shortcode('table-chart', [__CLASS__, 'handle_table_chart_shortcode']);
        }
    }

    /**
     * Handle Shortcode [table-chart id=<ID> /] in `the_content()`.
     *
     * @since 0.6
     *
     * @param array $shortcode_atts List of attributes that where included in the Shortcode.
     *
     * @return string Generated HTML code for the chart with the ID <ID>.
     */
    public static function handle_table_chart_shortcode($shortcode_atts)
    {
        // Generate the attribute query array for the template tag function.
        $table_query = [
            'chartist' => true,
        ];
        // Pass all parameters to the template tag parameters.
        foreach ((array) $shortcode_atts as $attribute => $value) {
            // Prepend 'chartist_' to all Shortcode attributes that the Extension understands.
            if (isset(self::$shortcode_attributes[$attribute])) {
                $attribute = 'chartist_'.$attribute;
            }
            $table_query[$attribute] = $value;
        }

        if ( function_exists( 'tablepress_get_table' )) {
            return tablepress_get_table($table_query);
        }
        return false;
    }

    /**
     * Load Chartist JavaScript and CSS files.
     *
     * @since 0.1
     */
    public static function enqueue_scripts_styles()
	{
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'table-chart')) {
        $dir = plugin_dir_url(__FILE__);
        wp_enqueue_script('chartist-js', $dir.'libdist/chartist.min.js', ['jquery'], self::$version, true);
        wp_enqueue_script('chartist-tooltip-plugin', $dir.'libdist/chartist-plugin-tooltip.min.js', ['chartist-js'], self::$version, true); // Add this line
        wp_enqueue_style('chartist-css', $dir.'libdist/chartist.min.css', [], self::$version);
        wp_enqueue_style('chartist-tooltip-css', $dir.'libdist/chartist-plugin-tooltip.css', ['chartist-css'], self::$version); // And this line
        if (file_exists(WP_CONTENT_DIR.'/tablepress-chartist-custom.css')) {
            wp_enqueue_style('chartist-custom-css', content_url('tablepress-chartist-custom.css'), ['chartist-css'], self::$version);
        }
    }
	}

    /**
     * Add the Extension's parameters as valid [table /] Shortcode attributes.
     *
     * @since 0.1
     *
     * @param array $default_atts Default attributes for the TablePress [table /] Shortcode.
     *
     * @return array Extended attributes for the Shortcode.
     */
    public static function register_shortcode_attributes($default_atts)
    {
        $default_atts['chartist'] = false;
        foreach (self::$shortcode_attributes as $attribute => $value) {
            $default_atts['chartist_'.$attribute] = $value;
        }

        return $default_atts;
    }

    /**
     * Generate the HTML and JavaScript code for a Chartist chart, based on the data of the given table.
     *
     * @since 0.1
     *
     * @param string $output         The generated HTML for the table.
     * @param array  $table          The current table.
     * @param array  $render_options The render options for the table.
     *
     * @return string The generated HTML and JavaScript code for the chart.
     */
    public static function generate_chart($output, $table, $render_options)
{
    if (!$render_options['chartist']) {
        return $output;
    }

    $json_chart_options = [];

    switch (strtolower($render_options['chartist_chart'])) {
        case 'bar':
            $chart = 'Bar';
            break;
        case 'pie':
            $chart = 'Pie';
            $json_chart_options[] = 'labelInterpolationFnc: function( value ) { return value; }';
            break;
        case 'donut':
            $chart = 'Pie';
            $json_chart_options[] = 'labelInterpolationFnc: function( value ) { return value; }';
            $json_chart_options[] = 'donut: true';
            break;
        case 'percent':
            $chart = 'Pie';
            $json_chart_options[] = "labelInterpolationFnc: function( value ) { return Math.round( value / data.series.reduce( sum ) * 100 ) + '%'; }";
            break;
        case 'piepercent':
            $chart = 'Pie';
            $json_chart_options[] = "labelInterpolationFnc: function( value, index ) { return value + ' (' + Math.round(data.series[index] / data.series.reduce( sum ) * 100) + '%)';}";
            break;
        case 'donutpercent':
            $chart = 'Pie';
            $json_chart_options[] = "labelInterpolationFnc: function( value, index ) { return value + ' (' + Math.round(data.series[index] / data.series.reduce( sum ) * 100) + '%)';}";
            $json_chart_options[] = 'donut: true';
            break;
        case 'line':
        default:
            $chart = 'Line';
            break;
    }

    $animation_script = <<<'JS'
chart.on('draw', function(data) {
	%s
});
JS;

    switch (strtolower($render_options['chartist_animation'])) {
        case 'buildup':
            $animation = <<<'JS'
if(data.type === 'line' || data.type === 'area') {
	data.element.animate({
		d: {
			begin: 1500 * data.index,
			dur: 1500,
			from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
			to: data.path.clone().stringify(),
			easing: Chartist.Svg.Easing.easeOutQuint
		}
	});
}
if (data.type === 'bar') {
	data.element.attr({
		style: 'stroke-width: 0px'
	});
	var strokeWidth = 10;

	for (var s = 0; s < data.series.length; ++s) {
		if (data.seriesIndex === s) {
			data.element.animate({
				y2:             {
					begin:  s * 1500,
					dur:    1500,
					from:   data.y1,
					to:     data.y2,
					easing: Chartist.Svg.Easing.easeOutSine
				},
				'stroke-width': {
					begin: s * 1500,
					dur:   1,
					from:  0,
					to:    strokeWidth,
					fill:  'freeze'
				}
			}, false);
		}
	}
}
JS;
            $animation_script = sprintf($animation_script, $animation);
            break;

        default:
            $animation_script = '';
    }

    $maxValue = null;
    foreach ($table['data'] as $row_idx => $row) {
        foreach ($row as $col_idx => $cell) {
            $numericValue = self::_maybe_string_to_number($cell);
            $table['data'][$row_idx][$col_idx] = $numericValue;
            if (is_numeric($numericValue) && ($maxValue === null || $numericValue > $maxValue)) {
                $maxValue = $numericValue;
            }
        }
    }

    if ($render_options['table_head']) {
        $json_labels = array_shift($table['data']);
    }

    if ('Pie' === $chart) {
        $table['data'] = array_shift($table['data']);
    }

    $json_chart_data = [
        'series' => $table['data'],
    ];
    if ($render_options['table_head'] && 'percent' !== $render_options['chartist_chart']) {
        $json_chart_data['labels'] = $json_labels;
    }

    $json_chart_data = json_encode((object) $json_chart_data);

    foreach (self::$attribute_to_js_mapping as $option_key => $option_js) {
        $option_key = 'chartist_'.$option_key;
        if (isset($render_options[$option_key])) {
            $value = self::_maybe_string_to_number($render_options[$option_key]);
            $json_chart_options[] = $option_js.': '.json_encode($value);
        }
    }

    // Find the maximum value in the table data
    $maxVal = 0;
    foreach ($table['data'] as $row) {
        foreach ($row as $cell) {
            if (is_numeric($cell) && $cell > $maxVal) {
                $maxVal = $cell;
            }
        }
    }
	// Increase the maximum value by 5%
    $maxVal *= 1.05;

    // Set the 'high' option to the new maximum value
    $json_chart_options[] = 'high: ' . $maxVal;

    $json_chart_options[] = 'plugins: [Chartist.plugins.tooltip()]';
    $json_chart_options = '{ '.implode(', ', $json_chart_options).' }';

    $aspect_ratio = 'ct-perfect-fourth';
    if (isset(self::$aspect_ratios[$render_options['chartist_aspect_ratio']])) {
        $aspect_ratio = self::$aspect_ratios[$render_options['chartist_aspect_ratio']];
    }

    $chartist_script = <<<JS
<script type="text/javascript">
jQuery(document).ready(function(){
	var	data = {$json_chart_data},
		options = {$json_chart_options},
		sum = function( a, b ) { return a + b; };
	var chart = new Chartist.{$chart}( '#chartist-{$render_options['html_id']}', data, options );
	chart.on('draw', function(data) {
    if(data.type === 'line' || data.type === 'area') {
        data.element.animate({
            d: {
                begin: 1500 * data.index,
                dur: 1500,
                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                to: data.path.clone().stringify(),
                easing: Chartist.Svg.Easing.easeOutQuint
            }
        });
    }
    if (data.type === 'bar') {
        data.element.attr({
            style: 'stroke-width: 0px'
        });
        var strokeWidth = 10;

        for (var s = 0; s < data.series.length; ++s) {
            if (data.seriesIndex === s) {
                data.element.animate({
                    y2:             {
                        begin:  s * 1500,
                        dur:    1500,
                        from:   data.y1,
                        to:     data.y2,
                        easing: Chartist.Svg.Easing.easeOutSine
                    },
                    'stroke-width': {
                        begin: s * 1500,
                        dur:   1,
                        from:  0,
                        to:    strokeWidth,
                        fill:  'freeze'
                    }
                }, false);
            }
        }
    }
    if(data.type === 'point') {
    data.element._node.onmouseover = function() {
        var tooltip = document.createElement('div');
        tooltip.className = 'chartist-tooltip';
        document.body.appendChild(tooltip);

        tooltip.style.left = (event.pageX - 40) + 'px';
        tooltip.style.top = (event.pageY - 55) + 'px';

        tooltip.style.transition = 'opacity 0.3s ease-in-out';

        tooltip.style.opacity = 1;
        tooltip.textContent = data.value.y !== undefined ? data.value.y : (data.value.x !== undefined ? data.value.x : data.value);

        data.element._node.tooltip = tooltip;
    };
    data.element._node.onmouseout = function() {
        this.tooltip.style.opacity = 0;

        setTimeout(function() {
            document.body.removeChild(this.tooltip);
        }.bind(this), 200);
    };
}

});

	{$animation_script}
});
</script>
JS;

    $chartist_divtag = sprintf(
        "<div id=\"%s\" class=\"ct-chart %s\"></div>\n",
        "chartist-{$render_options['html_id']}",
        $aspect_ratio
    );

    return $chartist_divtag.$chartist_script;
}


    /**
     * Convert a string to int or float, if it's a numeric string.
     *
     * @since 0.6
     *
     * @param string $string String that shall be converted to a number.
     *
     * @return mixed Possibly converted string.
     */
    protected static function _maybe_string_to_number($string)
    {
        if (!is_numeric($string)) {
            return $string;
        }

        if ($string == (int) $string) { // Don't do explicit === check here!
            return (int) $string;
        } else {
            return (float) $string;
        }
    }
} // class TablePress_Chartist
