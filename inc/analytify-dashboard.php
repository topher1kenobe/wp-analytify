<?php
/**
 * Analytify Dashboard file.
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
$wp_analytify   = new WP_Analytify();

$start_date_val = strtotime( '- 30 days' );
$end_date_val   = strtotime( 'now' );
$start_date     = date( 'Y-m-d', $start_date_val );
$end_date       = date( 'Y-m-d', $end_date_val );

if ( filter_input( INPUT_POST, 'view_data' ) && wp_verify_nonce( filter_input( INPUT_POST, 'analytify_dashboard_nonce' ), 'analytify_dashboard_action' ) ) {

	$s_date   = filter_input( INPUT_POST, 'st_date' );
	$ed_date  = filter_input( INPUT_POST, 'ed_date' );

}

if ( isset( $s_date ) ) {
	$start_date = $s_date;
}

if ( isset( $ed_date ) ) {
	$end_date = $ed_date;
}

// Fetch Dashboard Profile ID.
$dashboard_profile_id = get_option( 'pt_webprofile_dashboard' );

?>
<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo esc_url( plugins_url( 'images/wp-analytics-logo.png', dirname( __FILE__ ) ) );?>" alt=""></span>
		<?php esc_html_e( 'Analytify Dashboard', 'wp-analytify' ); ?>
    </h2>
	<?php

	if ( wpa_check_profile_selection( 'Analytify' ) ) { return; }

	$access_token  = get_option( 'post_analytics_token' );
	if ( $access_token ) {

	?>
    <div id="col-container">
        <div class="metabox-holder">
            <div class="postbox" style="width:100%;">
                    <div id="main-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox ">
                            <div class="handlediv" title="Click to toggle"><br />
                            </div>
                            <h3 class="hndle">
                                <span>
								<?php

								if ( get_option( 'wp-analytify-dashboard-profile-name' ) ) {
									echo sprintf( esc_html__( 'Complete Statistics of the Site (%1$s) and profile view (%4$s) Starting From %2$s to %3$s', 'wp-analytify' ), get_option( 'pt_webprofile_url' ), date( 'jS F, Y', strtotime( $start_date ) ), date( 'jS F, Y', strtotime( $end_date ) ), get_option( 'wp-analytify-dashboard-profile-name' ) ); } else {
									echo sprintf( esc_html__( 'Complete Statistics of the Site (%1$s) Starting From %2$s to %3$s', 'wp-analytify' ), get_option( 'pt_webprofile_url' ), date( 'jS F, Y', strtotime( $start_date ) ), date( 'jS F, Y', strtotime( $end_date ) ) ); }

								?>
                                </span>
                            </h3>
                            <div class="inside">
                                <div class="pa-filter">
									<form action="" method="post">
            							<?php wp_nonce_field( 'analytify_dashboard_action', 'analytify_dashboard_nonce' );?>

										<input type="text" id="st_date" name="st_date" value="<?php echo esc_attr( $start_date ); ?>">
										<input type="text" id="ed_date" name="ed_date" value="<?php echo esc_attr( $end_date ); ?>">
                                        <input type="submit" id="view_data" name="view_data" value="View Stats" class="button-primary btn-green">
                                    </form>
                                </div>

                                <!-- <a target="_blank" class="wrap_pro_section" href="http://wp-analytify.com/upgrade-from-free" title="Upgrade to PRO to enjoy full features of Analytify.">

                                    <div class="popup">
                                        <h2>Get PRO Version!</h2>
                                        <p>Impressed ? <br />This feature is limited to PRO users only.<br/>Click here to see the details.</p>
                                    </div>
                                    <div class="background"></div>

									<img class="gray-areas" src="<?php echo esc_url( plugins_url( 'images/live-stats-preview.png', dirname( __FILE__ ) ) );?>" width="100%" height="auto" alt="Upgrade to PRO to enjoy full features of Analytify." />
                                </a> -->

								<?php

								// General stats.
								$stats = get_transient( md5( 'show-overall-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
				                if ( false === $stats ) {

									$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users', $start_date, $end_date );
									set_transient( md5( 'show-overall-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $stats, 60 * 60 * 20 );
				                }

								if ( isset( $stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/general-stats.php';
									pa_include_general( $wp_analytify, $stats );
								}

								// End General stats.
								// Top Pages stats.
								$top_page_stats = get_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
				                if ( false === $top_page_stats ) {
				                	$top_page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', false, 5 );
				                	set_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $top_page_stats, 60 * 60 * 20 );

				                }

								if ( isset( $top_page_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/top-pages-stats.php';
									pa_include_top_pages_stats( $wp_analytify, $top_page_stats );
								}
								// End Top Pages stats.
								// Country stats.
								$country_stats = get_transient( md5( 'show-country-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
								if ( false === $country_stats ) {
									$country_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', false, 5 );
									set_transient( md5( 'show-country-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $country_stats, 60 * 60 * 20 );
								}

								if ( isset( $country_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/country-stats.php';
									pa_include_country( $wp_analytify,$country_stats );
								}


								// End Country stats.
								// City stats.
								$city_stats = get_transient( md5( 'show-city-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
								if ( false === $city_stats ) {
										$city_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', false, 5 );
										set_transient( md5( 'show-city-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $city_stats, 60 * 60 * 20 );
								}

								if ( isset( $city_stats->totalsForAllResults ) ) {
									  include ANALYTIFY_ROOT_PATH . '/views/admin/city-stats.php';
									  pa_include_city( $wp_analytify,$city_stats );
								}

								// Keywords stats.
								$keyword_stats = get_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );

								if ( false === $keyword_stats ) {
									$keyword_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 10 );
									set_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $keyword_stats, 60 * 60 * 20 );
								}

								if ( isset( $keyword_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/keywords-stats.php';
									pa_include_keywords( $wp_analytify,$keyword_stats );
								}

								// End Keywords stats.
								// Browser stats.
								$browser_stats = get_transient( md5( 'show-browser-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );
								if ( false === $browser_stats ) {
									$browser_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',false,5 );
									set_transient( md5( 'show-browser-dashboard' . $dashboard_profile_id . $start_date . $end_date ) , $browser_stats, 60 * 60 * 20 );
								}

								if ( isset( $browser_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/browser-stats.php';
									pa_include_browser( $wp_analytify,$browser_stats );
								}

								// End Browser stats.
								// Operating System Stats
								$operating_stats = get_transient( md5( 'show-operating-dashboard' . $dashboard_profile_id . $start_date . $end_date ) );

								if ( false === $operating_stats ) {
									$operating_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', false, 5 );
									set_transient( md5( 'show-operating-dashboard' . $dashboard_profile_id . $start_date . $end_date ), $operating_stats, 60 * 60 * 20 );
								}

								if ( isset( $city_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/os-stats.php';
									pa_include_operating( $wp_analytify,$operating_stats );
								}

								// End Operating System Stats
								// Mobile Stats
								$mobile_stats = get_transient( md5( 'show-mobile-dashborad' . $dashboard_profile_id . $start_date . $end_date ) );

								if ( false === $mobile_stats ) {
									$mobile_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:mobileDeviceInfo', '-ga:sessions', false, 5 );
									set_transient( md5( 'show-mobile-dashborad' . $dashboard_profile_id . $start_date . $end_date ), $mobile_stats, 60 * 60 * 20 );
								}

								if ( isset( $city_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/mobile-stats.php';
									pa_include_mobile( $wp_analytify,$mobile_stats );
								}

								// End Mobile Stats
								// Referral stats.
								$referr_stats = get_transient( md5( 'show-referral-dashborad' . $dashboard_profile_id . $start_date . $end_date ) );

								if ( $referr_stats ) {
										$referr_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 10 );
										set_transient( md5( 'show-referral-dashborad' . $dashboard_profile_id . $start_date . $end_date ), $referr_stats, 60 * 60 * 20 );
								}

								if ( isset( $referr_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH.'/views/admin/referrers-stats.php';
									pa_include_referrers( $wp_analytify,$referr_stats );
								}

								// End Referral stats.
								// Exit stats.
								$page_stats = get_transient( md5( 'show-page-dashborad' . $dashboard_profile_id . $start_date . $end_date ) );
								$top_page_stats = get_transient( md5( 'show-top-page-dashborad' . $dashboard_profile_id . $start_date . $end_date ) );

								if ( $top_page_stats && false === $page_stats ) {
									$page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:entrances,ga:pageviews,ga:exits', $start_date, $end_date, 'ga:PagePath', '-ga:exits', false, 5 );
									$top_page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', false, 5 );

									set_transient( md5( 'show-page-dashborad' . $dashboard_profile_id . $start_date . $end_date ) , $page_stats , 60 * 60 * 20 );
									set_transient( md5( 'show-top-page-dashborad' . $dashboard_profile_id . $start_date . $end_date ) , $top_page_stats , 60 * 60 * 20 );
								}

								if ( isset( $page_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/pages-stats.php';
									pa_include_pages_stats( $wp_analytify, $page_stats );
								}

								// End Exit stats.
								?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
	} else {
		esc_html_e( 'You must be authenticate to see the Analytics Dashboard.', 'wp-analytify' );
	}

?>
</div>
<script type="text/javascript">

jQuery(document).ready(function ($) {

    $("#st_date").datepicker({
                        dateFormat : 'yy-mm-dd',
                        changeMonth : true,
                        changeYear : true,
                        beforeShow: function() {
                             $('#ui-datepicker-div').addClass('mycalander');
                     },
                        yearRange: '-9y:c+nn',
						defaultDate: "<?php echo esc_js( $start_date );?>"
                });

    $("#ed_date").datepicker({
                        dateFormat : 'yy-mm-dd',
                        changeMonth : true,
                        changeYear : true,
                        beforeShow: function() {
                             $('#ui-datepicker-div').addClass('mycalander');
                     },
                        yearRange: '-9y:c+nn',
						defaultDate: "<?php echo esc_js( $end_date ); ?>"
                });
});

jQuery(window).resize(function(){
    drawChart();
    drawRegionsMap();
});
</script>
