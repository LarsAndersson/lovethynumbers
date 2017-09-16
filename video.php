<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
	
	//load start session for storage
	if(isset($global_user) && $global_user->user_email != '') {

	if(isset($_GET['id'])) {
		$video = new Video($_GET['id']);
	} else {
		$video = '';	
	} 

	//handle post/updates
	if($_POST) {
		//check if update or add
		if($video != '') {
				//update
				$data = $_POST;
				//calculate the path for the video
				$path = $video->files_directory.$_POST['user_id']."/".$video->video_id."/";

				//if there has been a user change, move the file directory to the new user
				if($video->user_id != $_POST['user_id']) {
					//create the new folder path
					mkdir($path, 0777, true);
					//move the folder to the new user
					rename($video->files_directory.$video->user_id."/".$video->video_id."/", $path);
					
					//update the image dir and the file dir with the correct new path
					$data['video_image'] = $path . preg_replace('/^.+?\/([^\/]+?)$/', '$1', $video->video_image);
					$data['video_file'] =  $path . preg_replace('/^.+?\/([^\/]+?)$/', '$1', $video->video_file);
				}
				
				//handle the file image uploads
				if(isset($_FILES['video_image']['name']))
				{
					//if no errors...
					if(!$_FILES['video_image']['error'])
					{
						
						//remove the old file
						unlink($path.$video->video_image);
						
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['video_image']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$image_path = $path.$new_file_name;
						move_uploaded_file($_FILES['video_image']['tmp_name'], $image_path);
						$data['video_image'] = $image_path;
					} else {
						//echo $_FILES['video_image']['error'];
					}
				}
				
				//handle the video file upload
				if(isset($_FILES['video_file']['name']))
				{
					//if no errors...
					if(!$_FILES['video_file']['error'])
					{
						//remove the old file
						unlink($path.$video->video_file);
						
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['video_file']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$file_path = $path.$new_file_name;
						move_uploaded_file($_FILES['video_file']['tmp_name'], $file_path);
						$data['video_image'] = $video_path;
					} else {
						//echo $_FILES['video_image']['error'];
					}
				}
				
				$data['video_xml'] = $path.$global_video_xml_filen_name;
				
				$video->updateVideo($data);
				
				//create all the required files for kprano
				//move the folders in place
				$new_video = new Video($_GET['id']);
				
				$global_helper->recurseCopy($global_video_template_folder, $path);
				
				//update the XML data with correct details
				$file = $data['video_xml'];
				
				//set up the settings for this particular video, just the image and file name
				$settings_array = array(
					'video_file' => substr($data['video_file'], strrpos($data['video_file'], '/') + 1),
					'video_image' =>  substr($data['video_image'], strrpos($data['video_image'], '/') + 1),
				);
				
				//apply each setting to the video file
				foreach($settings_array as $key=>$value) {
					file_put_contents($file,str_replace('{{'.$key.'}}',$value,file_get_contents($file)));
				}
				
				$_SESSION['splash'] = $_['video_updated'];
				$_SESSION['splash_type'] = 'success';
				
				//reload the user data after update
				$video = new Video($_GET['id']);
		} else {
				//new movie, all the stuffs
				$data = $_POST;
				$video = new Video();
				
				//calculate the path for the video
				$path = $video->files_directory.$_POST['user_id']."/".($video->getLastId()['video_id']+1)."/";
				//create the path
				
				if (!file_exists($path)) {
	    			mkdir($path, 0777, true);
				}
				
				//handle the file image uploads
				if(isset($_FILES['video_image']['name']))
				{
					//if no errors...
					if(!$_FILES['video_image']['error'])
					{
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['video_image']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$image_path = $path;
						$image_path .= $new_file_name;
						
						//move the uploaded file into location
						move_uploaded_file($_FILES['video_image']['tmp_name'], $image_path);
						$data['video_image'] = $image_path;
					} else {
						//echo $_FILES['video_image']['error'];
					}
				}
				
				//handle the video file upload
				if(isset($_FILES['video_file']['name']))
				{
					//if no errors...
					if(!$_FILES['video_file']['error'])
					{
						//now is the time to modify the future file name and validate the file
						$new_file_name = strtolower($_FILES['video_file']['name']); //rename file
						
						//if the file has passed the test
						//move it to where we want it to be
						$video_path = $path;
						$video_path .= $new_file_name;
						
						move_uploaded_file($_FILES['video_file']['tmp_name'], $video_path);
						$data['video_file'] = $video_path;
					} else {
						//echo $_FILES['video_image']['error'];
					}
				}

				$data['video_xml'] = $path.$global_video_xml_filen_name;

				//create all the required files for kprano
				//move the folders in place
				$global_helper->recurseCopy($global_video_template_folder, $path);
				
				//update the XML data with correct details
				$file = $data['video_xml'];
				
				//set up the settings for this particular video, just the image and file name
				$settings_array = array(
					'video_file' => substr($data['video_file'], strrpos($data['video_file'], '/') + 1),
					'video_image' =>  substr($data['video_image'], strrpos($data['video_image'], '/') + 1),
				);
				
				//apply each setting to the video file
				foreach($settings_array as $key=>$value) {
					file_put_contents($file,str_replace('{{'.$key.'}}',$value,file_get_contents($file)));
				}

				$video->addVideo($data);
				$_SESSION['splash'] = $_['video_video_added'];
				$_SESSION['splash_type'] = 'success';
				header("Location: video?id=".$video->getLastId());
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
							<?php echo $_['video_video']; ?> > <?php echo isset($video->video_title) ? $video->video_title : $_['video_add_new_video']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index.html"><?php echo $_['global_dashboard']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li><a href="#"><?php echo $_['video_videos']; ?></a></li>
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
								<li class="active"><a href="#tab_1_3" data-toggle="tab"><?php echo $_['video_video']; ?></a></li>
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
															<form action="" method="post" id="video_form" enctype="multipart/form-data">
																<input type="hidden" name="video_id" value="<?php echo isset($video->video_id) ? $video->video_id : '' ; ?>"/>
																<div class="control-group">
																	<label class="control-label"><?php echo $_['video_user']; ?></label>
																	<div class="controls">
																		<select class="span6 m-wrap" tabindex="1" name="user_id">
																			<?php foreach($global_user->getUsers() as $user) { 
																				$user = new User($user['user_id']);
																				?>
																			<option <?php echo ((isset($video->user_id) && $user->user_id == $video->user_id) || (isset($_GET['user_id']) && $_GET['user_id'] == $user->user_id)) ? 'selected=selected' : '' ; ?> value="<?php echo $user->user_id; ?>"><?php echo $user->getFullName(); ?></option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																
																<label class="control-label"><?php echo $_['video_title']; ?></label>
																<input type="text" name="video_title" value="<?php echo isset($video->video_id) ? $video->video_title : '' ; ?>" placeholder="Title" class="m-wrap span8" />
																
																<label class="control-label"><?php echo $_['video_length']; ?></label>
																<input type="text" name="video_length" value="<?php echo isset($video->video_id) ? $video->video_length : '' ; ?>" placeholder="11:11" class="m-wrap span8" />
																
																<label class="control-label"><?php echo $_['video_image']; ?></label>
																<?php if(isset($video->video_id) && $video->video_image != '') { ?>
																	<img style="max-width:140px;height: auto;" src='<?php echo $video->video_image; ?>' /><br />
																<?php }?>
																<input type="file" name="video_image">
																
																<br /><br />
																<label class="control-label"><?php echo $_['video_file']; ?> (<?php echo $_['video_max_file_size']." ".ini_get('upload_max_filesize'); ?>)</label>
																<?php if(isset($video->video_id) && $video->video_file != '') {
																	#echo $video->video_file."<br />";
																 }?>
																<input type="file" name="video_file">
																
																<br />
																<br />
																<?php 
																if(isset($video->video_id) && file_exists($video->getFilePath())) { ?>
																<iframe width="560" height="315" src="<?php echo $video->getVideoUrl(); ?>" frameborder="0" allowfullscreen></iframe>
																<br />
																<label class="control-label"><?php echo $_['video_embed']; ?></label>
																<div>
																	<xmp><iframe width="560" height="315" src="<?php echo $video->getVideoUrl(); ?>" frameborder="0" allowfullscreen></iframe></xmp>
																</div>
																
																<label class="control-label"><?php echo $_['video_share']; ?></label>
																<div>
																	<ul class="share-buttons">
																	  <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($video->getVideoUrl()); ?>&t=<?php echo urlencode($video->video_name); ?>" title="<?php echo $_['video_share_on_facebook']; ?>" target="_blank"><img src="assets/img/social/simple/Facebook.png"></a></li>
																	  <li><a href="https://twitter.com/intent/tweet?source=<?php echo urlencode($video->getVideoUrl()); ?>&text=<?php echo urlencode($video->video_name); ?>: <?php echo urlencode($video->getVideoUrl()); ?>" target="_blank" title="<?php echo $_['video_share_on_twitter']; ?>"><img src="assets/img/social/simple/Twitter.png"></a></li>
																	  <li><a href="https://plus.google.com/share?url=<?php echo urlencode($video->getVideoUrl()); ?>" target="_blank" title="<?php echo $_['video_share_on_google']; ?>"><img src="assets/img/social/simple/Google+.png"></a></li>
																	  <li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($video->getVideoUrl()); ?>&title=<?php echo urlencode($video->video_name); ?>&summary=&source=<?php echo urlencode($video->getVideoUrl()); ?>" target="_blank" title="<?php echo $_['video_share_on_linkedin']; ?>"><img src="assets/img/social/simple/LinkedIn.png"></a></li>
																	</ul>
																</div>
																<br />
																<div><a href='<?php echo $video->getVideoUrl(); ?>'><?php echo $video->getVideoUrl(); ?></a></div>
																<br />
																<?php } ?>
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