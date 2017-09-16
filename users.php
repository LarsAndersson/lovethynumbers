<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in

	//only load page for actual users, ink superuser
	if(isset($global_user) && $global_user->user_email != '' && $global_user->user_level < 5) {

	if($_POST) {
		if(isset($_POST['action']) && $_POST['action'] == 'delete') {
			if(isset($_POST['delete_id'])) {
				$id = $_POST['delete_id'];
				$global_user->deleteUser($id);
				$_SESSION['splash'] = $_['users_deleted'];
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
							<?php echo $_['users_users']; ?>
						</h3>
						<ul class="breadcrumb">
							<li>
								<i class="icon-home"></i>
								<a href="index"><?php echo $_['global_home']; ?></a> 
								<i class="icon-angle-right"></i>
							</li>
							<li>
								<a href="#"><?php echo $_['users_users']; ?></a>
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
								<div class="caption"><i class="icon-edit"></i><?php echo $_['users_users']; ?></div>
							</div>
							<div class="portlet-body">
								<div class="table-toolbar">
									<div class="btn-group">
										<a href='profile'>
										<button id="sample_editable_1_new" class="btn green">
										<?php echo $_['users_add_new']; ?> <i class="icon-plus"></i>
										</button>
										</a>
									</div>
								</div>
								<div id="sample_editable_1_wrapper" class="dataTables_wrapper form-inline" role="grid">
									<div class="row-fluid"><div class="span6">
										<table class="table table-striped table-hover table-bordered dataTable" id="sample_editable_1" aria-describedby="sample_editable_1_info">
									<thead>
										<tr role="row">
											<th class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" aria-label="Username"><?php echo $_['global_name']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Full Name: activate to sort column ascending"><?php echo $_['global_last_name']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Notes: activate to sort column ascending"><?php echo $_['global_user_level']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Edit: activate to sort column ascending" ><?php echo $_['global_edit']; ?></th>
											<th class="sorting" role="columnheader" tabindex="0" aria-controls="sample_editable_1" rowspan="1" colspan="1" aria-label="Delete: activate to sort column ascending"><?php echo $_['global_delete']; ?></th>
										</tr>
									</thead>
								
								<tbody role="alert" aria-live="polite" aria-relevant="all">
								<?php 
								$count = 1;
								foreach($global_user->getUsers() as $user) {
									$user = new User($user['user_id']);
								?>
									<tr class="<?php echo ($count % 2 != 0) ? 'odd' : 'even'; ?>">
										<td class=" sorting_1"><?php echo $user->getFullName(); ?></td>
										<td class=" "><?php echo $user->user_last; ?></td>
										<td class="center "><?php echo ($user->user_level < 5)?$_['profile_admin'] : $_['profile_customer']; ?></td>
										<td class=" "><a class="edit" href="profile?id=<?php echo $user->user_id; ?>"><?php echo $_['global_edit']; ?></a></td>
										<td class=" ">
											<?php if($user->user_id != 1) { ?>
											<form method='post' action=''>
												<input name='delete_id' type='hidden' value='<?php echo $user->user_id; ?>' />
												<input name='action' type='hidden' value='delete' />
												<button id="sample_editable_1_delete" class="btn red" type='submit'>
													<?php echo $_['global_delete']; ?> <i class="icon-minus"></i>
												</button>
											</form>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
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