<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in

	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '' && ($global_user->user_level < 5 || (isset($_GET['id']) && $_GET['id'] == $global_user->user_id))) {

	//fetch the user for this particular profile, if there is none, leave blank
	if(isset($_GET['id'])) {
		$user = new User($_GET['id']);	
	} else {
		$user = '';
	}
	
	//handle post/updates
	if($_POST) {
		//if it is a password update - update the password, else update the user
		if(isset($_POST['current_password']) && isset($_POST['new_password_control']) && isset($_POST['user_password'])) {
			//make sure they used the right / existing password
			//if(md5($_POST['current_password']) == $user->user_password) {
				if(md5($_POST['current_password']) == $user->user_password) {
				//make sure they typed the same password twice
				if($_POST['user_password'] == $_POST['new_password_control']) {
					$user->updateUser($_POST);
					$_SESSION['splash'] = $_['profile_password_updated'];
					$_SESSION['splash_type'] = 'success';
					
					//reload the user data after update
					$user = new User($_GET['id']);
					
				} else {
					$_SESSION['splash'] = $_['profile_password_no_match'];
					$_SESSION['splash_type'] = 'warning';
				}
			} else {
				$_SESSION['splash'] = $_['profile_wrong_password'];
				$_SESSION['splash_type'] = 'error';
			}
		} else {
			//check if update or add
			if($user != '') {
				//update
				$data = $_POST;
				$data['user_id'] = $_GET['id'];
				$user->updateUser($data);
				$_SESSION['splash'] = $_['profile_updated'];
				$_SESSION['splash_type'] = 'success';
				
				//reload the user data after update
				$user = new User($_GET['id']);
			} else {
				$data = $_POST;
				$global_user->addUser($data);
				$_SESSION['splash'] = $_['profile_user_added'];
				$_SESSION['splash_type'] = 'success';
			}
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
							<?php echo $_['profile_user_profile']; ?> > <?php echo isset($user->user_id) ? $user->getFullName() : $_['profile_add_new_user']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['global_my_profile']; ?></a></li>
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
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['profile_account']; ?></a></li>
								<?php if($global_user->user_level < 5) { ?>
								<?php } ?>
								<!-- <li><a href="#tab_1_6" data-toggle="tab">Help</a></li> -->
							</ul>
							<div class="tab-content">
								<!--end tab-pane-->
								<!--tab_1_2-->
								<div class="tab-pane row-fluid profile-account active" id="tab_1_3">
									<div class="row-fluid">
										<div class="span12">
											<div class="span3">
												<ul class="ver-inline-menu tabbable margin-bottom-10">
													<li class="active">
														<a data-toggle="tab" href="#tab_1-1">
														<i class="icon-cog"></i> 
														<?php echo $_['profile_personal_info']; ?>
														</a> 
														<span class="after"></span>                                    
													</li>
													<!-- <li ><a data-toggle="tab" href="#tab_2-2"><i class="icon-picture"></i><?php echo $_['profile_change_avatar']; ?></a></li> -->
													<?php if(isset($user->user_id)) { ?>
													<li ><a data-toggle="tab" href="#tab_3-3"><i class="icon-lock"></i><?php echo $_['profile_change_password']; ?></a></li>
													<?php } ?>
													<!-- <li ><a data-toggle="tab" href="#tab_4-4"><i class="icon-eye-open"></i> Privacity Settings</a></li> -->
												</ul>
											</div>
											<div class="span9">
												<div class="tab-content">
													<div id="tab_1-1" class="tab-pane active">
														<div style="height: auto;" id="accordion1-1" class="accordion collapse">
															<form action="" method="post" id="profile_form">
																<input type="hidden" name="user_id" value="<?php echo isset($user->user_id) ? $user->user_id : '' ; ?>"/>
																<input type="hidden" name="user_zip" value="<?php echo isset($user->user_zip) ? $user->user_zip : '' ; ?>"/>
																<input type="hidden" name="user_fax" value="<?php echo isset($user->user_fax) ? $user->user_fax : '' ; ?>"/>
																<input type="hidden" name="user_active" value="<?php echo isset($user->user_active) ? $user->user_active : '' ; ?>"/>
																<input type="hidden" name="user_city" value="<?php echo isset($user->user_city) ? $user->user_city : '' ; ?>"/>
																<input type="hidden" name="user_image" value="<?php echo isset($user->user_image) ? $user->user_image : '' ; ?>"/>
																<label class="control-label"><?php echo $_['profile_first_name']; ?></label>
																<input type="text" name="user_first" value="<?php echo isset($user->user_id) ? $user->user_first : '' ; ?>" placeholder="John" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_last_name']; ?></label>
																<input type="text" name="user_last" value="<?php echo isset($user->user_id) ? $user->user_last : '' ; ?>" placeholder="Doe" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_phone']; ?></label>
																<input type="text" name="user_phone" value="<?php echo isset($user->user_id) ? $user->user_phone : '' ; ?>" placeholder="+1 646 580 DEMO (6284)" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_company']; ?></label>
																<input type="text" name="user_company" value="<?php echo isset($user->user_id) ? $user->user_company : '' ; ?>" placeholder="Rabadaba AB" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_email']; ?></label>
																<input type="text" name="user_email" value="<?php echo isset($user->user_id) ? $user->user_email : '' ; ?>" placeholder="test@test.com" class="m-wrap span8" />
																<?php if($global_user->user_level < 5) { ?>
																	<div class="control-group">
																		<label class="control-label"><?php echo $_['profile_admin_level']; ?></label>
																		<div class="controls">
																			<select class="span6 m-wrap" tabindex="1" name="user_level">
																				<option <?php echo (!isset($user->user_level)) ? 'selected=selected' : '' ; ?> value="10"><?php echo $_['profile_admin_level_select']; ?></option>
																				<option <?php echo (isset($user->user_level) && $user->user_level<5) ? 'selected=selected' : '' ; ?> value="2"><?php echo $_['profile_admin']; ?></option>
																				<option <?php echo (isset($user->user_level) && $user->user_level>=5) ? 'selected=selected' : '' ; ?> value="10"><?php echo $_['profile_customer']; ?></option>
																			</select>
																		</div>
																	</div>
																	<label class="control-label"><?php echo $_['profile_vat_number']; ?></label>
																	<input type="text" name="user_vat_number" value="<?php echo isset($user->user_id) ? $user->user_vat_number : '' ; ?>" placeholder="SE999999999901" class="m-wrap span8" />
																	<label class="control-label"><?php echo $_['profile_account']; ?></label>
																	<input type="text" name="user_account" value="<?php echo isset($user->user_id) ? $user->user_account : '' ; ?>" placeholder="111-1111" class="m-wrap span8" />
																	<?php 
																	//if no user is selected, make the password select available easily
																	if(!isset($user->user_id)) { ?>
																		<label class="control-label"><?php echo $_['password']; ?></label>
																		<input type="text" name="user_password" placeholder="<?php echo $_['password']; ?>" class="m-wrap span8" />
																	<?php } ?>
																	<label class="control-label"><?php echo $_['profile_notes']; ?></label>
																	<textarea type="text" name="user_note" placeholder="" class="m-wrap span8" /><?php echo isset($user->user_id) ? $user->user_note : '' ; ?></textarea>
																<?php } ?>
																<div class="submit-btn">
																	<input type='submit' class="btn green" value="<?php echo $_['global_save']; ?>">
																	<!-- <a href="#" class="btn"><?php echo $_['global_cancel']; ?></a> -->
																</div>
															</form>
														</div>
													</div>
													<div id="tab_3-3" class="tab-pane">
														<div style="height: auto;" id="accordion3-3" class="accordion collapse">
															<form action="" method="post" id="password_form">
																<input type="hidden" name="user_id" value="<?php echo isset($user->user_id) ? $user->user_id : '' ; ?>"/>
																<label class="control-label"><?php echo $_['profile_current_password']; ?></label>
																<input type="text" name="current_password" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_new_password']; ?></label>
																<input type="text" name="user_password" class="m-wrap span8" />
																<label class="control-label"><?php echo $_['profile_write_password_again']; ?></label>
																<input type="text" name="new_password_control" class="m-wrap span8" />
																<div class="submit-btn">
																	<input type='submit' class="btn green" value="<?php echo $_['profile_change_password']; ?>">
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
								<div class="tab-pane" id="tab_1_4">
									<div class="row-fluid add-portfolio">
										<div class="pull-left">
											<a href="content<?php echo isset($user->user_id) ? '?user_id='.$user->user_id : '' ; ?>" class="btn icn-only green"><?php echo $_['profile_add_new_content']; ?> <i class="m-icon-swapright m-icon-white"></i></a>                          
										</div>
									</div>
								</div>
								<div class="tab-pane" id="tab_1_5">
									<div class="row-fluid add-portfolio">
										<div class="pull-left">
											<a href="screen<?php echo isset($user->user_id) ? '?user_id='.$user->user_id : '' ; ?>" class="btn icn-only green"><?php echo $_['profile_add_new_screen']; ?> <i class="m-icon-swapright m-icon-white"></i></a>                          
										</div>
									</div>
									<!--end add-portfolio-->
									<!-- <div class="row-fluid portfolio-block">
										<div class="span5 portfolio-text">
											<img src="assets/img/profile/portfolio/logo_metronic.jpg" alt="" />
											<div class="portfolio-text-info">
												<h4>Metronic - Responsive Template</h4>
												<p>Lorem ipsum dolor sit consectetuer adipiscing elit.</p>
											</div>
										</div>
										<div class="span5" style="overflow:hidden;">
											<div class="portfolio-info">
												Today Sold
												<span>187</span>
											</div>
											<div class="portfolio-info">
												Total Sold
												<span>1789</span>
											</div>
											<div class="portfolio-info">
												Earns
												<span>$37.240</span>
											</div>
										</div>
										<div class="span2 portfolio-btn">
											<a href="#" class="btn bigicn-only"><span>Manage</span></a>                      
										</div>
									</div> -->
									<!--end row-fluid-->
								</div>
								<!-- END TAB PANE -->
								
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