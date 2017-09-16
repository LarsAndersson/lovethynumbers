<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
  	$widget_helper = new Widget();
	
	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '') {

	if(isset($_GET['id'])) {
		$widget = new Widget($_GET['id']);
	} else {
		$widget = '';	
	}
	
	if(isset($_GET['new'])) {
		$_SESSION['splash'] = $_['widget_widget_added'];
		$_SESSION['splash_type'] = 'success';
	}

	//handle post/updates
	if($_POST) {
		//check if update or add
		if($widget != '') {
				//update
				$data = $_POST;
				$widget->update($data);
				$widget = new Widget($_GET['id']);
				$_SESSION['splash'] = $_['widget_widget_updated'];
				$_SESSION['splash_type'] = 'success';
		} else {
				//new movie, all the stuffs
				$data = $_POST;
				$widget = new Widget();
				$widget->add($data);
				$widget_data['widget_id'] = $widget->getLastId();
				$widget_data['user_id'] = $_POST['user_id']; 
				$_SESSION['splash'] = $_['widget_widget_added'];
				$_SESSION['splash_type'] = 'success';
				header("Location: widget?new=1&id=".$widget->getLastId());
		}
	}
?>

<!-- END HEAD -->
<?php
	include 'templates/head.php';
?>

<!-- BEGIN BODY -->
<body class="page-header-fixed">
	<?php
		include 'templates/header.php';
	?>
	<!-- BEGIN CONTAINER -->
	<div class="page-container">
		<?php
			include 'templates/navigation.php';
		?>
		<!-- END SIDEBAR -->
		<!-- BEGIN PAGE -->
		<div class="page-content">
			<!-- BEGIN PAGE CONTAINER-->
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<h3 class="page-title">
							<?php echo $_['widget_widget']; ?> > <?php echo isset($widget->widget_title) ? $widget->widget_title : $_['feed_add_new_feed']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['widget_widgets']; ?></a></li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				<div class="row-fluid profile">
					<?php include 'templates/splash.php'; ?>
					
					<div class="span12">
						<!--BEGIN TABS-->
						<div class="tabbable tabbable-custom tabbable-full-width">
							<ul class="nav nav-tabs">
								<!-- <li><a href="#tab_1_1" data-toggle="tab">Overview</a></li>
								<li><a href="#tab_1_2" data-toggle="tab">Profile Info</a></li> -->
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['widget_widget']; ?></a></li>
								<!-- <li><a href="#tab_1_6" data-toggle="tab">Help</a></li> -->
							</ul>
							<div class="tab-content">
								<!--end tab-pane-->
								<!--tab_1_2-->
								<div class="tab-pane row-fluid profile-account active" id="tab_1_3">
									<div class="row-fluid">
										<div class="span12">
											<div class="span12">
												<div class="tab-content">
													<div id="tab_1-1" class="tab-pane active">
														<div style="height: auto;" id="accordion1-1" class="accordion collapse">
															<form action="" method="post" id="feed_form" enctype="multipart/form-data">
																<input type="hidden" name="widget_id" value="<?php echo isset($widget->widget_id) ? $widget->widget_id : '' ; ?>"/>
																<div class="control-group">
																	<?php if($global_user->user_level < 5) { ?>
																	<label class="control-label"><?php echo $_['widget_user']; ?></label>
																	<div class="controls">
																		<select class="span6 m-wrap" tabindex="1" name="user_id">
																			<?php foreach($global_user->getUsers() as $user) { 
																				$user = new User($user['user_id']);
																				?>
																			<option <?php echo ((isset($widget->user_id) && $user->user_id == $widget->user_id) || (isset($_GET['user_id']) && $_GET['user_id'] == $user->user_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $user->user_id; ?>"><?php echo $user->getFullName(); ?></option>
																			<?php } ?>
																		</select>
																	</div>
																	<?php } else { ?>
																	<input type="hidden" name="user_id" value="<?php echo $global_user->user_id; ?>"/>	
																	<?php } ?>
																</div>
																
																<label class="control-label"><?php echo $_['widget_type']; ?></label>
																<select onchange='showValidFeeds();' name='widget_type_id' id='widget_type_id'>
																	<option value=''><?php echo $_['global_select_none']; ?></option>
																	<?php foreach($widget_helper->getWidgetTypes() as $widget_type) { ?>
																	<option
																	<?php echo ((isset($widget->widget_type_id) && $widget_type['widget_type_id'] == $widget->widget_type_id)) ? 'selected=selected' : '' ; ?> 
																	data-required='<?php echo $widget_type['valid_feeds']; ?>' 
																	value='<?php echo $widget_type['widget_type_id']; ?>'>
																		<?php echo $_['widget_type_'.$widget_type['type_label']]; ?>
																	</option>
																<?php } ?>
																</select>
																<span id="required_fields"></span>
																
																
																<label class="control-label"><?php echo $_['widget_feed_to_use']; ?></label>
																<select name='feed_id' id='widget_type_id'>
																	<option value=''><?php echo $_['global_select_none']; ?></option>
																	<?php foreach($widget_helper->getFeedsByUser($global_user->user_id) as $feeds) {
																		$feed = new Feed($feeds['feed_id']);
																		?>
																	<option
																	<?php echo ((isset($widget->feed_id) && $feed->feed_id == $widget->feed_id)) ? 'selected=selected' : '' ; ?> 
																	data-required='<?php echo $feed->feed_type_id; ?>' 
																	value='<?php echo $feed->feed_id; ?>'>
																		<?php echo $_['feed_type_'.$feed->getFeedType($feed->feed_type_id)['type_label']]; ?> - <?php echo $feed->feed_title; ?>
																	</option>
																<?php } ?>
																</select>
																
																<label class="control-label"><?php echo $_['widget_title']; ?></label>
																<input type="text" name="widget_title" value="<?php echo isset($widget->widget_id) ? $widget->widget_title : '' ; ?>" placeholder="<?php echo $_['widget_title_description']; ?>" class="m-wrap span8" />
																
																<br /><br />
																<div class="submit-btn">
																	<input type='submit' class="btn green" value="<?php echo $_['global_save']; ?>">
																	<a href="feeds" class="btn"><?php echo $_['global_cancel']; ?></a>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
											<!--end span9-->                                   
										</div>
									</div>
								</div>
								<!--end tab-pane-->
							</div>
						</div>
						<!--END TABS-->
					</div>
				</div>
				<!-- END PAGE CONTENT-->
			</div>
			<!-- END PAGE CONTAINER--> 
		</div>
		<!-- END PAGE -->    
	</div>
	<!-- END CONTAINER -->
	<script type='text/javascript'>
		function showValidFeeds() {
			jQuery("#required_fields").html('');
			required_values = jQuery("#widget_type_id option:selected").attr('data-required');
			if(!!required_values) {
				jQuery("#required_fields").html('<?php echo $_['widget_valid_fields']; ?>' + required_values);
			}
		}
	</script>
	
	<?php include 'templates/footer.php'; ?>
	
<?php 
//close the page load user check
} else {
	header('Location: login');
}
 ?>