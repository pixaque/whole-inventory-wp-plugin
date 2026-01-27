<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Main', false ) ) :
	/**
	 * Class Main
	 */
	class Main {
		/**
     * The hook to add an action to add our meta boxes.
     *
     */
    const ADD_HOOK = 'wpse57092_add_boxes';

    /**
     * The page key for our meta boxes.  You could use this as the "group" for
     * the settings API as well or something similar.
     *
     */
    const PAGE = 'wp_wer_pk_main';

    /**
     * The setting key.
     *
     */
    const SETTING = 'wpse57092_opts';

    public static function init()
    {

        add_action(
            self::ADD_HOOK,
            array(__CLASS__, 'meta_box')
        );

        add_action(
            'admin_init',
            array(__CLASS__, 'settings')
        );
    }

    public static function settings()
    {
        
        register_setting(
            self::PAGE,
            self::SETTING,
            array(__CLASS__, 'validate')
        );

        add_settings_section(
            'default',
            __('A Settings Section', 'wer_pk'),
            '__return_false',
            self::PAGE
        );

        add_settings_field(
            'wpse57092-text',
            __('Some Field', 'wer_pk'),
            array(__CLASS__, 'field_cb'),
            self::PAGE,
            'default',
            array('label_for' => self::SETTING)
        );
    }

    public static function meta_box()
    {
        add_meta_box(
            'custom-meta-wpse57092',
            __('Welcome to Inventory Management Dashboard.', 'wer_pk'),
            array(__CLASS__, 'box_cb'),
            self::PAGE,
            'main',
            'high'
        );

        add_meta_box(
            'custom-meta-wpse57094',
            __('Orders Details', 'wer_pk'),
            array(__CLASS__, 'box_cb3'),
            self::PAGE,
            'main',
            'high'
        );

         add_meta_box(
            'custom-meta-wpse57093',
            __('Sales Overview', 'wer_pk'),
            array(__CLASS__, 'box_cb2'),
            self::PAGE,
            'main',
            'high'
        );
    }

    public static function box_cb($setting)
    {
        // do_settings_fields doesn't do form tables for you.
        echo __("This inventory management plugin aims to empower wholesalers, Shop Owners, distributors and Stock Managers by providing a comprehensive toolset for managing their commodities efficiently. With its intuitive design and essential features, they can streamline their operations, improve inventory accuracy, and strengthen their relationships with distributors. As you experience this plugin, consider incorporating user feedback to enhance functionality and user experience further.", "wer_pk");
    }

     public static function box_cb2($setting) {

        $currencyName = esc_attr(Settings::get_currency_symbol());
        ?>
        
        

        <script type="text/javascript">
            window.onload = function () {

                var dataPoints = [];
                var stockChart = new CanvasJS.StockChart("stockChartContainer", {
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text:""
                },
                subtitles: [{
                    text:""
                }],
                charts: [{
                    axisX: {
                    crosshair: {
                        enabled: true,
                        snapToDataPoint: true,
                        valueFormatString: "MMM YYYY"
                    }
                    },
                    axisY: {
                    title: "<?php echo sprintf(__('Million of %s', "wer_pk"), $currencyName); ?>",
                    prefix: "<?php echo esc_attr(Settings::get_currency_symbol()) . ' '; ?>",
                    suffix: "",
                    crosshair: {
                        enabled: true,
                        snapToDataPoint: true,
                        valueFormatString: "<?php echo esc_attr(Settings::get_currency_symbol()).' #,###.00'; ?>",
                    }
                    },
                    data: [{
                    type: "line",
                    xValueFormatString: "MMM YYYY",
                    yValueFormatString: "<?php echo esc_attr(Settings::get_currency_symbol()).' #,###.##00'; ?>",
                    dataPoints : dataPoints
                    }]
                }],
                navigator: {
                    slider: {
                    minimum: new Date(2010, 00, 01),
                    maximum: new Date(2018, 00, 01)
                    }
                }
                });
  
                jQuery.getJSON(ajax_object.ajax_url + "?action=get_expenses_aggrigation", function(data) {
    
                    for(var i = 0; i < data.length; i++){
                        dataPoints.push({x: new Date(data[i].date), y: Number(data[i].sale)});
                    }

                    stockChart.render();
                });

                var dataPoints1 = [];
  

                var chart = new CanvasJS.Chart("chartContainer", {
	                exportEnabled: true,
	                animationEnabled: true,
	                title:{
		                text: ""
	                },
	                legend:{
		                cursor: "pointer",
		                itemclick: explodePie
	                },
	                data: [{
		                type: "doughnut",
		                showInLegend: true,
                        legendText: "{name} Orders",
		                toolTipContent: "{name} Orders: <strong>{y}%</strong>",
		                indexLabel: "{name} Orders - {y}%",
		                dataPoints: dataPoints1
	                }]
                });

                

                jQuery.getJSON(ajax_object.ajax_url + "?action=getOrdersData", function(data) {
    
                    for(var i = 0; i < data.length; i++){
                        dataPoints1.push({y: data[i].y, name: data[i].name, exploded: data[i].exploded});
                    }

                    console.log(dataPoints1);

                    chart.render();
                });

            }

            function explodePie (e) {
	            if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		            e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	            } else {
		            e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	            }
	            e.chart.render();

            }

        </script>

        <div id="stockChartContainer" style="height: 400px; margin: 0px auto;"></div>

        <?php

    }

     public static function box_cb3($setting)
    {
        ?>

            <div id="chartContainer" style="height: 300px; width: 100%;"></div>

        <?php

    }

    public static function field_cb($args)
    {
        printf(
            '<input type="text" id="%1$s" name="%1$s" class="widefat" value="%2$s" />',
            esc_attr($args['label_for']),
            esc_attr(get_option($args['label_for']))
        );
        echo '<p class="description">';
        _e('Just some help text here', 'wer_pk');
        echo '</p>';
    }

    public static function page_cb()
    {
        do_action(self::ADD_HOOK);
        do_action('dummyNotice');
        ?>
        <div class="wrap metabox-holder">
            <?php
                do_meta_boxes(self::PAGE, 'main', self::SETTING);
            ?>

        </div>
        <?php
    }

    public static function validate($dirty)
    {
        return esc_url_raw($dirty);
    }
	}
endif;
