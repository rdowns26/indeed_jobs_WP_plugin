<?php

/*
Plugin Name: Indeed Jobs Plugin
Plugin URI: http://www.indeed.com
Description: A WordPress Plugin that displays Indeed jobs on your company careers page.
Version: 1.0
Author: Indeed Hackathon Team
Author URI: http://www.indeed.com
License: GPL2
*/

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.indeed.com
 * @since             1.0.0
 * @package           Test_Indeed_Jobs_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Test Indeed Jobs Plugin
 * Plugin URI:        http://www.indeed.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Indeed Hackathon Team
 * Author URI:        http://www.indeed.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       test-indeed-jobs-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-test-indeed-jobs-plugin-activator.php
 */
function activate_test_indeed_jobs_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-test-indeed-jobs-plugin-activator.php';
	Test_Indeed_Jobs_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-test-indeed-jobs-plugin-deactivator.php
 */
function deactivate_test_indeed_jobs_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-test-indeed-jobs-plugin-deactivator.php';
	Test_Indeed_Jobs_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_test_indeed_jobs_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_test_indeed_jobs_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-test-indeed-jobs-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_test_indeed_jobs_plugin() {

	$plugin = new Test_Indeed_Jobs_Plugin();
	$plugin->run();

}
run_test_indeed_jobs_plugin();

// Adding Plugin to Admin Menu
add_action( 'admin_menu', 'my_plugin_menu' );

function my_plugin_menu() {
	add_menu_page( 'Indeed Jobs Plugin', 'Indeed Jobs', 'manage_options', 'indeed-jobs', 'indeed_jobs_plugin_options', plugins_url('test-indeed-jobs-plugin/public/img/favicon.png'));
}

// Creation of Plugin Admin Page
function indeed_jobs_plugin_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>The Indeed Jobs Plugin lets you effortlessly display your jobs from Indeed on your wordpress website.</p>';
	echo '</div>';

	// COMPANY NAME FORM ENTRY
	$hidden_field_name = 'mt_submit_hidden';
	$company_name = 'company_id';
	$company_field_name = 'company_id';

	// Read in existing option value from database
	$company_val = get_option( $company_name);

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if ( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// Read their posted value
		$company_val = $_POST[ $company_field_name];

		// Save the posted value in the database
		update_option( $company_name, $company_val );

		// Put a "settings saved" message on the screen
		?>
		<div class="updated"><p><strong><?php _e( 'Settings saved.', 'menu-test' ); ?></strong></p></div>
		<?php

	}


	echo '<div class="wrap">';

	echo "<h2>" . __( 'Indeed Jobs Plugin Settings', 'menu-test' ) . "</h2>";
	?>
	
	<hr/>

	<h2>Step 1: Create a Free Account on Indeed and Post A Job</h2>

	<p>Posting a job is <i>free</i> on Indeed. Simply create an account and post your jobs to start receiving qualified candidates. </p>
	<p>If you already have an Indeed account and jobs, skip to step #2.</p>

	<a href="https://employers.indeed.com/post-job" class="btn-admin-settings" onmousedown="this.href = appendParamsOnce(this.href, '');">Get Started With Indeed</a>
	<hr/>

	<h2>Step 2: Show Your Jobs Anywhere on Your Site</h2>
	<form name="form1" method="post" action="">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
		<p><?php _e( "Company Name:", 'menu-test' ); ?>
			<input type="text" name="<?php echo $company_field_name; ?>" value="<?php echo $company_val; ?>" size="20">
		</p>
		<p style="font-size: .8em;color: red">Please input your company name exactly as you did when creating your job posting.</p>
		<p><input type="submit" name="Submit" class="btn-admin-settings" value="<?php esc_attr_e( 'Save Changes' ) ?>"/></p>
	</form>

	<p>Now, paste the following shortcode into the text box in a page, post, or widget! </p>
	<p><strong>[indeed-jobs]</strong></p>

	<hr/>

	<h2>Step 3: Manage & Post More Jobs Through Indeed</h2>
	<br>
	<a href="https://employers.qa.indeed.net/m#dashboard" class="btn-admin-settings" onmousedown="this.href = appendParamsOnce(this.href, '');">Post and Manage Your Jobs</a>
	</div>
	
	<hr/>
	<?php
}

function display_indeed_jobs(){
	require ("indeed-api-call.php");

	global $load_click_js;
	$load_click_js = true;

	$publisher_val = ######## TAKEN OUT FOR SECURITY REASONS #######; 
	$company_name = 'company_id';
	$company_val = get_option( $company_name);

	$client = new Indeed($publisher_val);

	$params = array(
		"q" => "company:(" . $company_val . ")",
		"l" => "",
		//"limit" => "25",
		"userip" => $_SERVER['REMOTE_ADDR'],
		"useragent" => $_SERVER['HTTP_USER_AGENT'] 
	);
	
	$results = $client->search($params);

	function unbold($string) {
		$start = str_replace("<b>", "", $string);
		return str_replace("</b>", "", $start);
	}

	function getJobSummary($url) {
		$header = '<span class="summary">';
		$footer = '</span>';

		$html = file_get_contents($url);
		$startPosition = strpos($html, '<span id="job_summary" class="summary">');
		$endPosition = strpos($html, '</span>', $startPosition);
		$length = $endPosition - $startPosition-39;
		$body = substr($html, $startPosition+39, $length);

		return $header . $body . $footer;
	}

	function formatJobTitle($jobtitle, $url, $jobkey, $onmousedown) {
		$header = "<div id='jl_$jobkey'>";
		$body = "<a class='jobtitle' rel='nofollow' onmousedown=\"$onmousedown\" href=\"$url\" title=\"$url\">$jobtitle</a>";
		$footer = '</div>';
		return $header . $body . $footer;
	}

	function formatCompany($company) {
		$header = '<span class="company"><span itemprop="name">';
		$body = $company;
		$footer = '</span></span>';
		return $header . $body . $footer;

	}

	function formatLocation($location) {
		$header = '<span><span class="location"><span>';
		$body = $location;
		$footer = '</span></span></span>';
		return $header . $body . $footer;

	}

	function formatSnippet($snippet, $jobkey) {
		$header  = "<div id='sn_$jobkey'><span class='summary' itemprop='description'>";
		$body = $snippet;
		$footer = "</span></div>";
		return $header . $body . $footer;
	}

	function formatTime($time) {
		$header = '<span class="date">';
		$body = $time;
		$footer = '</span>';
		return $header . $body . $footer;
	}

	function formatBottomLine($time) {
		return formatTime($time);
	}

    function formatShowMore($jobSummary, $jobkey) {
	    $body = "<div class='showmorecontent' id='cn_$jobkey' style='display: none;'>$jobSummary</div>";
        $link = "<a href='javascript:void(0)' class='showmorebutton' id='bt_$jobkey'>Show More</a>";
        return "$body<div>$link</div>";
    }

	function formatResult($result) {
		$jobkey = $result["jobkey"];
		$jobtitle = $result["jobtitle"];
		$location = $result["formattedLocationFull"];
		$company = $result["company"];
		$snippet = unbold($result["snippet"],"<b>","");
		$url = $result["url"];
		$time = $result["formattedRelativeTime"];
		#$jobSummary = getJobSummary($url);
		$onmousedown = $result["onmousedown"];
		
		$header = '<div id="p_' . $jobkey . '">';
		$footer = '</div>';
        $body = formatJobTitle($jobtitle, $url, $jobkey, $onmousedown)
            . formatCompany($company)
            . ' - '
            . formatLocation($location)
            . formatSnippet($snippet, $jobkey)
            #. formatShowMore($jobSummary, $jobkey)
            . formatBottomLine($time);

		return $header . $body . $footer;
	}
	if (count($results)>1) { //if valid api search results	
		foreach ($results["results"] as $value) {
			echo formatResult($value);
			echo "<hr class='jobseparator'/>";
		}
		echo '<span id="indeed_at"><a href="http://www.indeed.com/">jobs</a> by <a href="http://www.indeed.com/" title="Job Search"><img src="http://www.indeed.com/p/jobsearch.gif" style="border: 0; vertical-align: middle;" alt="Indeed job search"></a></span>';
	} else {
		echo implode(",",$results);
	}
}
add_shortcode('indeed-jobs', 'display_indeed_jobs');

add_action('init', 'register_click_js');
add_action('wp_footer', 'print_click_js');

function register_click_js() {
	wp_register_script('indeed-click-tracking', '//gdc.indeed.com/ads/apiresults.js', array(), '1.0', true);
}

function print_click_js() {
	global $load_click_js;
	if ( ! $load_click_js )
		return;
	wp_print_scripts('indeed-click-tracking');
}
?>
