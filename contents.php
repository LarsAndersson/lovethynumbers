<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	$content_helper = new Content();
	
	//only load page for actual users, ink superuser
	if(isset($global_user) && $global_user->user_email != '') {

	if($_POST) {
		
		if(isset($_POST['action']) && $_POST['action'] == 'delete') {
			if(isset($_POST['delete_id'])) {
				$id = $_POST['delete_id'];
				$content = new Content($id);
				$content->deleteContent($id);
				$_SESSION['splash'] = $_['content_deleted'];
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
		<div class="page-content" style="min-height:864px !important">
			<!-- BEGIN PAGE CONTAINER-->        
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->
				<div class="row-fluid">
					
					<?php include 'templates/splash.php'; ?>
					
					<div class="span12">
						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<h3 class="page-title">
							<?php echo $_['contents_contents']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index"><?php echo $_['global_home']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li>
								<a href="#"><?php echo $_['contents_contents']; ?></a>
							</li>
						</ul>
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN EXAMPLE TABLE PORTLET-->
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption"><i class="icon-edit"></i><?php echo $_['contents_contents']; ?></div>
							</div>
							<div class="portlet-body">
								<div class="table-toolbar">
									<?php if($global_user->user_level < 5) { ?>
									<div class="btn-group">
										<a href='content'>
										<button id="sample_editable_1_new" class="btn green">
										<?php echo $_['contents_add_new']; ?> <i class="icon-plus"></i>
										</button>
										</a>
									</div>
									<?php } ?>
								</div>
								<div id="sample_editable_1_wrapper" class="dataTables_wrapper form-inline" role="grid">
									<div class="row-fluid"><div class="span6">
										<table class="table table-striped table-hover table-bordered dataTable" id="sample_editable_1" aria-describedby="sample_editable_1_info">
									<thead>
										<tr role="row">
											<th class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" aria-label="Username"><?php echo $_['contents_title']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Full Name: activate to sort column ascending"><?php echo $_['contents_image']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Edit: activate to sort column ascending" ><?php echo $_['global_edit']; ?></th>
											<?php if($global_user->user_level < 5) { ?>
												<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Delete: activate to sort column ascending"><?php echo $_['global_user']; ?></th>
												<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Delete: activate to sort column ascending"><?php echo $_['global_delete']; ?></th>
											<?php } ?>
										</tr>
									</thead>
								
								<tbody role="alert" aria-live="polite" aria-relevant="all">
								<?php 
								$count = 1;
								if($global_user->user_level < 5) {
									$contents = $content_helper->getContents();
								} else {
									$contents = $content_helper->getContentsByUser($global_user->user_id);	
								}
								foreach($contents as $content) {
									if(isset($content['content_id'])) {
									$content = new content($content['content_id']);
									?>
										<tr class="<?php echo ($count % 2 != 0) ? 'odd' : 'even'; ?>">
											<td class=" sorting_1"><?php echo $content->content_title; ?></td>
											<td class=" "><img style="max-width:140px;height: auto;" src='<?php echo $content->content_image; ?>'</td>
											<td class=" "><a class="edit" href="content?id=<?php echo $content->content_id; ?>"><?php echo $_['global_edit']; ?></a></td>
											<?php if($global_user->user_level < 5) { ?>
											<td class=" ">
												<?php echo $content->getUserNameById($content->user_id); ?>
											</td>
											<td class=" ">
												<form method='post' action=''>
													<input name='delete_id' type='hidden' value='<?php echo $content->content_id; ?>' />
													<input name='action' type='hidden' value='delete' />
													<button id="sample_editable_1_delete" class="btn red" type='submit'>
														<?php echo $_['global_delete']; ?> <i class="icon-minus"></i>
													</button>
												</form>
											</td>
											<?php } ?>
										</tr>
									<?php }
									}
								?>
								</tbody></table>
							</div>
						</div>
						<!-- END EXAMPLE TABLE PORTLET-->
					</div>
				</div>
				<!-- END PAGE CONTENT -->
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