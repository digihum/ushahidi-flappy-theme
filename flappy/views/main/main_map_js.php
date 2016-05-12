<?php
/**
 * Main cluster js file.
 * 
 * Server Side Map Clustering
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		
// Initialize the Ushahidi namespace
Ushahidi.baseURL = "<?php echo url::site(); ?>";

// To hold the Ushahidi.Map reference
var map = null;


/**
 * Toggle Layer Switchers
 */
function toggleLayer(link, layer) {
	if ($("#"+link).text() == "<?php echo Kohana::lang('ui_main.show'); ?>")
	{
		$("#"+link).text("<?php echo Kohana::lang('ui_main.hide'); ?>");
	}
	else
	{
		$("#"+link).text("<?php echo Kohana::lang('ui_main.show'); ?>");
	}
	$('#'+layer).toggle(500);
}

/**
 * Create a function that calculates the smart columns
 */
function smartColumns() {
	//Reset column size to a 100% once view port has been adjusted
	$("ul.content-column").css({ 'width' : "100%"});

	//Get the width of row
	var colWrap = $("ul.content-column").width();

	// Find how many columns of 200px can fit per row / then round it down to a whole number
	var colNum = 1;

	// Get the width of the row and divide it by the number of columns it 
	// can fit / then round it down to a whole number. This value will be
	// the exact width of the re-adjusted column
	var colFixed = Math.floor(colWrap / colNum);

	// Set exact width of row in pixels instead of using % - Prevents
	// cross-browser bugs that appear in certain view port resolutions.
	$("ul.content-column").css({ 'width' : colWrap});

	// Set exact width of the re-adjusted column	
	$("ul.content-column li").css({ 'width' : colFixed});
}

/**
 * Callback function for rendering the timeline
 */
function refreshTimeline(options) {

	<?php if (Kohana::config('settings.enable_timeline')) {?>

	// Use report filters if no options passed
	options = options || map.getReportFilters();
	// Copy options object to avoid accidental modifications to reportFilters
	options = jQuery.extend({}, options);

	var url = "<?php echo url::site().'json/timeline/'; ?>";

	if(options.i == undefined || options.i == '') { // HT: Added condition only to auto interval if empty interval type choosed
		var interval = (options.e - options.s) / (3600 * 24);
		if (interval <= 3) {
			options.i = "hour";
		} else if (interval <= (31 * 6)) {
			options.i = "day";
		} else {
			options.i = "month";
		}
	}
	// HT: More info link
	var urlLink = "<?php echo url::site().'reports/index/?'?>"+$.param(options);
	$('#timelineMoreLink').attr('href', urlLink);

	// Get the graph data
	$.ajax({
		url: url,
		data: options,
		success: function(response) {
			// Clear out the any existing plots
			$("#graph").html('');

			if (response != null && response[0].data.length < 2)
				return;

			var graphData = [];
			var raw = response[0].data;
			for (var i=0; i<raw.length; i++) {
				var date = new Date(raw[i][0]);

				var dateStr = date.getFullYear() + "-";
				dateStr += ('0' + (date.getMonth()+1)).slice(-2) + '-';
				dateStr += ('0' + date.getDate()).slice(-2);

				graphData.push([dateStr, parseInt(raw[i][1])]);
			}
			var timeline = $.jqplot('graph', [graphData], {
				seriesDefaults: {
					<?php if (Kohana::config('settings.timeline_graph') == 'bar') { ?>
					renderer: $.jqplot.BarRenderer, // HT: For bargraph
					rendererOptions: { // HT: For bargraph
						varyBarColor: true,
						barWidth: 1,
						shadowAlpha: 0
					},
					<?php } ?>
					color: response[0].color,
					lineWidth: 1.6,
					markerOptions: {
						<?php if (Kohana::config('settings.timeline_point_label')) { ?>
							show: true, // HT: To show the points
							//style: 'circle' // HT: Circle point
						<?php } else { ?>
							show: false,
						<?php } ?>
					},
					<?php if (Kohana::config('settings.timeline_point_label')) { ?>
						pointLabels: { // HT: To show point label
							show: true,
							edgeTolerance: -10,
							ypadding: 3
						}
					<?php } ?>
				},
				axesDefaults: {
					pad: 1.23,
				},
				axes: {
					xaxis: {
						renderer: $.jqplot.DateAxisRenderer,
						tickOptions: {
							formatString: '%#d&nbsp;%b\n%Y'
						}
					},
					yaxis: {
						min: 0,
						tickOptions: {
							formatString: '%.0f'
						}
					}
				},
				<?php if (Kohana::config('settings.timeline_point_label')) { ?>
					cursor: {show: true}, // HT: To show current point detail
				<?php } else { ?>
					cursor: {show: false},
				<?php } ?>
			});
		},
		dataType: "json"
	});
	<?php }?>
}


jQuery(function() {
	var reportsURL = "<?php echo Kohana::config('settings.allow_clustering') == 1 ? "json/cluster" : "json"; ?>";

	// Render thee JavaScript for the base layers so that
	// they are accessible by Ushahidi.js
	<?php echo map::layers_js(FALSE); ?>
	
	// Map configuration
	var config = {

		// Zoom level at which to display the map
		zoom: <?php echo Kohana::config('settings.default_zoom'); ?>,

		// Redraw the layers when the zoom level changes
		redrawOnZoom: <?php echo Kohana::config('settings.allow_clustering') == 1 ? "true" : "false"; ?>,

		// Center of the map
		center: {
			latitude: <?php echo Kohana::config('settings.default_lat'); ?>,
			longitude: <?php echo Kohana::config('settings.default_lon'); ?>
		},

		// Map controls
		mapControls: [
			new OpenLayers.Control.Navigation({ dragPanOptions: { enableKinetic: true } }),
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.Zoom(),
			new OpenLayers.Control.MousePosition({
				div: document.getElementById('mapMousePosition'),
				formatOutput: Ushahidi.convertLongLat
			}),
			new OpenLayers.Control.Scale('mapScale'),
			new OpenLayers.Control.ScaleLine(),
		],

		// Base layers
		baseLayers: <?php echo map::layers_array(FALSE); ?>,

		// Display the map projection
		showProjection: true,
	};

	// Initialize the map
	map = new Ushahidi.Map('welcome_map', config);
	map.addLayer(Ushahidi.GEOJSON, {
		name: "<?php echo Kohana::lang('ui_main.reports'); ?>",
		url: reportsURL,
		transform: false
	}, true, true);
	
	//Execute the function when page loads
	smartColumns();

});

$(window).resize(function () { 
	//Each time the viewport is adjusted/resized, execute the function
	smartColumns();
});