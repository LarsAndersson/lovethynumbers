<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
  	$content_helper = new Content();
	
	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '') {

	if(isset($_GET['id'])) {
		$screen = new Screen($_GET['id']);
	} else {
		$screen = '';	
	}
	
	if(isset($_GET['new'])) {
		$_SESSION['splash'] = $_['screen_screen_added'];
		$_SESSION['splash_type'] = 'success';
	}

	//handle post/updates
	if($_POST) {
		//check if update or add
		if($screen != '') {
				//update
				$data = $_POST;
				$screen->updateScreen($data);
				$screen = new Screen($_GET['id']);
				$_SESSION['splash'] = $_['screen_screen_updated'];
				$_SESSION['splash_type'] = 'success';
		} else {
				//new movie, all the stuffs
				$data = $_POST;
				$screen = new Screen();
				$screen->addScreen($data);
				$_SESSION['splash'] = $_['screen_screen_added'];
				$_SESSION['splash_type'] = 'success';
				header("Location: screen?new=1&id=".$screen->getLastId());
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
							<?php echo $_['screen_screen']; ?> > <?php echo isset($screen->screen_title) ? $screen->screen_title : $_['screen_add_new_screen']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['screen_screens']; ?></a></li>
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
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['screen_screen']; ?></a></li>
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
															<form action="" method="post" id="screen_form" enctype="multipart/form-data">
																<input type="hidden" name="screen_id" value="<?php echo isset($screen->screen_id) ? $screen->screen_id : '' ; ?>"/>
																<div class="control-group">
																	<?php if($global_user->user_level < 5) { ?>
																	<label class="control-label"><?php echo $_['screen_user']; ?></label>
																	<div class="controls">
																		<select class="span6 m-wrap" tabindex="1" name="user_id">
																			<?php foreach($global_user->getUsers() as $user) { 
																				$user = new User($user['user_id']);
																				?>
																			<option <?php echo ((isset($screen->user_id) && $user->user_id == $screen->user_id) || (isset($_GET['user_id']) && $_GET['user_id'] == $user->user_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $user->user_id; ?>"><?php echo $user->getFullName(); ?></option>
																			<?php } ?>
																		</select>
																	</div>
																	<?php } else { ?>
																	<input type="hidden" name="user_id" value="<?php echo $global_user->user_id; ?>"/>	
																	<?php } ?>
																</div>
																<label class="control-label"><?php echo $_['screen_reference_id']; ?></label>
																<input type="text" name="screen_reference_id" value="<?php echo isset($screen->screen_reference_id) ? $screen->screen_reference_id : '' ; ?>" placeholder="<?php echo $_['screen_reference_id_description']; ?>" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['screen_title']; ?></label>
																<input type="text" name="screen_title" value="<?php echo isset($screen->screen_id) ? $screen->screen_title : '' ; ?>" placeholder="<?php echo $_['screen_title_description']; ?>" class="m-wrap span8" />
																<div class="control-group">
																	<label class="control-label"><?php echo $_['screen_content']; ?></label>
																	<div class="controls">
																		<select class="span6 m-wrap" tabindex="1" name="content_id">
																			<?php if($global_user->user_level > 5) { ?>
																				<?php foreach($content_helper->getContentByUser($global_user->user_id) as $content) { 
																					$content = new Content($content['content_id']);
																					?>
																				<option <?php echo ((isset($screen->screen_id) && $content->content_id == $screen->content_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $content->content_id; ?>"><?php echo $content->content_title; ?></option>
																				<?php } ?>
																			<? } else { ?>
																				<?php foreach($content_helper->getContents() as $content) { 
																					$content = new Content($content['content_id']);
																					?>
																				<option <?php echo ((isset($screen->screen_id) && $content->content_id == $screen->content_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $content->content_id; ?>"><?php echo $content->content_title; ?></option>
																				<?php } ?>
																			<?php } ?>	 
																		</select>
																	</div>
																</div>
																<a href='<?php echo $screen->getViewerPath(); ?>' class="btn green"><?php echo $_['screen_view']; ?></a>
																<br /><br />
																<div class="submit-btn">
																	<input type='submit' class="btn green" value="<?php echo $_['global_save']; ?>">
																	<!-- <a href="#" class="btn"><?php echo $_['global_cancel']; ?></a> -->
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
	<?php include 'templates/footer.php'; ?>
	
<?php 
//close the page load user check
} else {
	header('Location: login');
}
 ?>