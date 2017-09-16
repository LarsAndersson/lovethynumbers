<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
  	$feed_helper = new Feed();
	
	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '') {

	if(isset($_GET['id'])) {
		$feed = new Feed($_GET['id']);
	} else {
		$feed = '';	
	}
	
	if(isset($_GET['new'])) {
		$_SESSION['splash'] = $_['feed_feed_added'];
		$_SESSION['splash_type'] = 'success';
	}

	//handle post/updates
	if($_POST) {
		//check if update or add
		if($feed != '') {
				//update
				$data = $_POST;
				$feed->update($data);
				$feed = new Feed($_GET['id']);
				$_SESSION['splash'] = $_['feed_feed_updated'];
				$_SESSION['splash_type'] = 'success';
		} else {
				//new movie, all the stuffs
				$data = $_POST;
				$feed = new Feed();
				$feed->add($data);
				$feed_data['feed_id'] = $feed->getLastId();
				$feed_data['user_id'] = $_POST['user_id']; 
				if(!empty($_FILES)) {
					$files = $_FILES;
					$feed->uploadFile($files, $feed_data);
				}
				$_SESSION['splash'] = $_['feed_feed_added'];
				$_SESSION['splash_type'] = 'success';
				header("Location: feed?new=1&id=".$feed->getLastId());
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
							<?php echo $_['feed_feed']; ?> > <?php echo isset($feed->feed_title) ? $feed->feed_title : $_['feed_add_new_feed']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['feed_feeds']; ?></a></li>
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
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['feed_feed']; ?></a></li>
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
																<input type="hidden" name="feed_id" value="<?php echo isset($feed->feed_id) ? $feed->feed_id : '' ; ?>"/>
																<div class="control-group">
																	<?php if($global_user->user_level < 5) { ?>
																	<label class="control-label"><?php echo $_['feed_user']; ?></label>
																	<div class="controls">
																		<select class="span6 m-wrap" tabindex="1" name="user_id">
																			<?php foreach($global_user->getUsers() as $user) { 
																				$user = new User($user['user_id']);
																				?>
																			<option <?php echo ((isset($feed->user_id) && $user->user_id == $feed->user_id) || (isset($_GET['user_id']) && $_GET['user_id'] == $user->user_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $user->user_id; ?>"><?php echo $user->getFullName(); ?></option>
																			<?php } ?>
																		</select>
																	</div>
																	<?php } else { ?>
																	<input type="hidden" name="user_id" value="<?php echo $global_user->user_id; ?>"/>	
																	<?php } ?>
																</div>
																
																<label class="control-label"><?php echo $_['feed_type']; ?></label>
																<select onchange='showRequired();' name='feed_type_id' id='feed_type_id'>
																	<option value=''><?php echo $_['global_select_none']; ?></option>
																<?php foreach($feed_helper->getFeedTypes() as $feed_type) { ?>
																	<option
																	<?php echo ((isset($feed->feed_type_id) && $feed_type['feed_type_id'] == $feed->feed_type_id)) ? 'selected=selected' : '' ; ?> 
																	data-required='<?php echo $feed_type['required_fields']; ?>' 
																	value='<?php echo $feed_type['feed_type_id']; ?>'>
																		<?php echo $_['feed_type_'.$feed_type['type_label']]; ?>
																	</option>
																<?php } ?>
																</select>
																<span id="required_fields"></span>
																<label class="control-label"><?php echo $_['feed_title']; ?></label>
																<input type="text" name="feed_title" value="<?php echo isset($feed->feed_id) ? $feed->feed_title : '' ; ?>" placeholder="<?php echo $_['feed_title_description']; ?>" class="m-wrap span8" />
																
																<label class="control-label"><?php echo $_['feed_file']; ?></label>
																<input type="file" name="feed_file" placeholder="<?php echo $_['feed_file_description']; ?>" class="m-wrap span8" />
																
																<br /><br />
																<div class="submit-btn">
																	<input type='submit' class="btn green" value="<?php echo $_['global_save']; ?>">
																	<a href="feeds" class="btn"><?php echo $_['global_cancel']; ?></a>
																	<?php echo isset($feed->feed_id) ? 'TODO - DATA INSPECTOR':''; ?>
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
		function showRequired() {
			jQuery("#required_fields").html('');
			required_values = jQuery("#feed_type_id option:selected").attr('data-required');
			if(!!required_values) {
				jQuery("#required_fields").html('<?php echo $_['feed_required_fields']; ?>' + required_values);
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