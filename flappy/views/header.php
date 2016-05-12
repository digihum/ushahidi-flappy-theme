<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" style="background:url('<?PHP echo url::site() . "media/background.jpg"; ?>'); background-repeat:no-repeat; background-size:cover; background-attachment:fixed;">
<head>
	<title><?php echo html::specialchars($page_title.$site_name); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<?php echo $header_block; ?>
	<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
	<link href='https://fonts.googleapis.com/css?family=Raleway:400,100,300,500,700' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?PHP echo url::site(); ?>themes/flappy/css/font-awesome.min.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function() {
			$( "#tabs" ).tabs();
		});
	</script>
	<script type="text/javascript">
	<?PHP
		$welcome_map_js = new View("main/main_map_js");
		echo $welcome_map_js;
	?>
	</script>
</head>
<?php
  // Add a class to the body tag according to the page URI

  // we're on the home page
  if (count($uri_segments) == 0)
  {
  	$body_class = "page-main";
  }
  // 1st tier pages
  elseif (count($uri_segments) == 1)
  {
    $body_class = "page-".$uri_segments[0];
  }
  // 2nd tier pages... ie "/reports/submit"
  elseif (count($uri_segments) >= 2)
  {
    $body_class = "page-".$uri_segments[0]."-".$uri_segments[1];
  }
?>

<body id="page" class="<?php echo $body_class; ?>">
	<!-- wrapper -->
	<div class="wrapper floatholder rapidxwpr">

				
		<!-- header -->
		<div id="header">

			<!-- logo -->
			<?php if ($banner == NULL): ?>
			<div id="logo">
				<h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<?php else: ?>
			<a href="<?php echo url::site();?>"><img src="<?php echo $banner; ?>" alt="<?php echo $site_name; ?>" /></a>
			<?php endif; ?>

        <?php
            // Action::header_item - Additional items to be added by plugins
	        Event::run('ushahidi_action.header_item');
        ?>
			
			<div>
				<!-- mainmenu -->
				<?php
					if (Kohana::config('settings.allow_reports'))
					{
						?>
						<div id="submitmenu">
							<ul id="showsubmit">
								<li>
									<a id="how-to-report-menu"><?php echo Kohana::lang('ui_main.how_to_report'); ?></a>
								</li>
							</ul>
							<div id="how-to-report-box" class="map-menu-box">
								<div class="how-to-report-methods">
									<?PHP
									
										$phone_array = array();
										$sms_no1 = Kohana::config('settings.sms_no1');
										$sms_no2 = Kohana::config('settings.sms_no2');
										$sms_no3 = Kohana::config('settings.sms_no3');
										if ( ! empty($sms_no1))
										{
											$phone_array[] = $sms_no1;
										}
										if ( ! empty($sms_no2))
										{
											$phone_array[] = $sms_no2;
										}
										if ( ! empty($sms_no3))
										{
											$phone_array[] = $sms_no3;
										}
										$this->template->content->phone_array = $phone_array;

										// Get external apps
										$external_apps = array();
										// Catch errors, in case we have an old db
										try {
											$external_apps = ORM::factory('externalapp')->find_all();
										}
										catch(Exception $e) {}
										
										$report_email = Kohana::config('settings.site_email');

									?>
								
									<!-- Phone -->
									<?php if (!empty($phone_array)) { ?>
									<div>
										<strong><?php echo Kohana::lang('ui_main.report_option_1'); ?></strong>
										<?php foreach ($phone_array as $phone) { ?>
											<?php echo $phone; ?>
										<?php } ?>
									</div>
									<?php } ?>
									
									<!-- External Apps -->
									<?php if (count($external_apps) > 0) { ?>
									<div>
										<strong><?php echo Kohana::lang('ui_main.report_option_external_apps'); ?>:</strong>
										<?php foreach ($external_apps as $app) { ?>
											<a href="<?php echo $app->url; ?>"><?php echo $app->name; ?></a>
										<?php } ?>
									</div>
									<?php } ?>

									<!-- Email -->
									<?php if (!empty($report_email)) { ?>
									<div>
										<strong><?php echo Kohana::lang('ui_main.report_option_2'); ?>:</strong><br/>
										<a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a>
									</div>
									<?php } ?>

									<!-- Twitter -->
									<?php if (!empty($twitter_hashtag_array)) { ?>
									<div>
										<strong><?php echo Kohana::lang('ui_main.report_option_3'); ?>:</strong><br/>
										<?php foreach ($twitter_hashtag_array as $twitter_hashtag) { ?>
											<span>#<?php echo $twitter_hashtag; ?></span>
											<?php if ($twitter_hashtag != end($twitter_hashtag_array)) { ?>
												<br />
											<?php } ?>
										<?php } ?>
									</div>
									<?php } ?>

									<!-- Web Form -->
									<div>
										<a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				
				<p id="menushow"><i class="fa fa-bars" aria-hidden="true"></i></p>
			
				<div id="mainmenu" class="clearingfix">
			
					<ul>
						<?php nav::main_tabs($this_page); ?>
						<?php echo $header_nav; ?>
					</ul>

					<?php if ($allow_feed == 1) { ?>
					<div class="feedicon"><a href="<?php echo url::site(); ?>feed/"><img alt="<?php echo html::escape(Kohana::lang('ui_main.rss')); ?>" src="<?php echo url::file_loc('img'); ?>media/img/icon-feed.png" style="vertical-align: middle;" border="0" /></a></div>
					<?php } ?>

				
					<div id="searchbox">
			
						<!-- languages -->
						<?php echo $languages;?>
						<!-- / languages -->

						<!-- searchform -->
						<?php echo $search; ?>
						<!-- / searchform -->

					</div>
					
				</div>
				
			</div>
			
		</div>
		<!-- main body -->
		<div id="middle">
			
				<!-- / mainmenu -->
