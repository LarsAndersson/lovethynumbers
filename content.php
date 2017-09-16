<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
	
	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '') {

	if(isset($_GET['id'])) {
		$content = new Content($_GET['id']);
	} else {
		$content = '';	
	}
	 
	if(isset($_GET['new'])) {
		$_SESSION['splash'] = $_['content_content_added'];
		$_SESSION['splash_type'] = 'success';
	}
	
	//handle post/updates
	if($_POST) {
		//check if update or add
		if($content != '') {
				//update
				$data = $_POST;
				//calculate the path for the content
				$path = $content->files_directory.$_POST['user_id']."/".$content->content_id."/";

				//if there has been a user change, move the file directory to the new user
				if($content->user_id != $_POST['user_id']) {
					//create the new folder path
					mkdir($path, 0777, true);
					//move the folder to the new user
					rename($content->files_directory.$content->user_id."/".$content->content_id."/", $path);
					
					//update the image dir and the file dir with the correct new path
					$data['content_image'] = $path . preg_replace('/^.+?\/([^\/]+?)$/', '$1', $content->content_image);
				}
				
				//handle the file image uploads
				if(isset($_FILES['content_image']['name']))
				{
					//if no errors...
					if(!$_FILES['content_image']['error'])
					{
						
						//remove the old file
						unlink($path.$content->content_image);
						
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['content_image']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$image_path = $path.$new_file_name;
						move_uploaded_file($_FILES['content_image']['tmp_name'], $image_path);
						$data['content_image'] = $image_path;
					} else {
						//echo $_FILES['content_image']['error'];
					}
				}
				
				$content->updateContent($data);
				
				//create all the required files for kprano
				//move the folders in place
				$new_content = new Content($_GET['id']);
				
				$global_helper->recurseCopy($global_content_template_folder, $path);
				
				//set up the settings for this particular content, just the image and file name
				$settings_array = array(
					'content_image' =>  substr($data['content_image'], strrpos($data['content_image'], '/') + 1),
				);
				
				//apply each setting to the content file
				foreach($settings_array as $key=>$value) {
					file_put_contents($file,str_replace('{{'.$key.'}}',$value,file_get_contents($file)));
				}
				
				$_SESSION['splash'] = $_['content_updated'];
				$_SESSION['splash_type'] = 'success';
				
				//reload the user data after update
				$content = new Content($_GET['id']);
		} else {
				//new movie, all the stuffs
				$data = $_POST;
				$content = new Content();
				
				//calculate the path for the content
				$path = $content->files_directory.$_POST['user_id']."/".($content->getLastId()['content_id']+1)."/";
				//create the path
				
				if (!file_exists($path)) {
	    			mkdir($path, 0777, true);
				}
				
				//handle the file image uploads
				if(isset($_FILES['content_image']['name']))
				{
					//if no errors...
					if(!$_FILES['content_image']['error'])
					{
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['content_image']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$image_path = $path;
						$image_path .= $new_file_name;
						
						//move the uploaded file into location
						move_uploaded_file($_FILES['content_image']['tmp_name'], $image_path);
						$data['content_image'] = $image_path;
					} else {
						//echo $_FILES['content_image']['error'];
					}
				}
				
				//set up the settings for this particular content, just the image and file name
				$settings_array = array(
					'content_image' =>  substr($data['content_image'], strrpos($data['content_image'], '/') + 1),
				);
				
				//apply each setting to the content file
				//update the XML data with correct details

				$content->addContent($data);
				header("Location: content?new=1&id=".$content->getLastId());
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
							<?php echo $_['content_content']; ?> > <?php echo isset($content->content_title) ? $content->content_title : $_['content_add_new_content']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['content_contents']; ?></a></li>
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
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['content_content']; ?></a></li>
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
															<form action="" method="post" id="content_form" enctype="multipart/form-data">
																<input type="hidden" name="content_id" value="<?php echo isset($content->content_id) ? $content->content_id : '' ; ?>"/>
																<div class="control-group">
																	<?php if($global_user->user_level < 5) { ?>
																	<label class="control-label"><?php echo $_['content_user']; ?></label>
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
																
																<label class="control-label"><?php echo $_['content_title']; ?></label>
																<input type="text" name="content_title" value="<?php echo isset($content->content_id) ? $content->content_title : '' ; ?>" placeholder="Title" class="m-wrap span8" />
																
																<label class="control-label"><?php echo $_['content_image']; ?></label>
																<?php if(isset($content->content_id) && $content->content_image != '') { ?>
																	<img style="max-width:140px;height: auto;" src='<?php echo $content->content_image; ?>' /><br />
																<?php }?>
																<input type="file" name="content_image">
																<br /><br />
																<?php if(isset($content->content_id) && $content->content_image != '') { ?>
																<a href='<?php echo $content->getViewerPath(); ?>'>Viewer Link</a>
																<?php } ?>
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