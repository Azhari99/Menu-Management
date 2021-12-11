<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

	<div class="row">
		<div class="col-lg-8">

			<form class="user" method="POST" action="<?= base_url('User/changepassword') ?>">
				<div class="form-group row">
					<label for="email" class="col-sm-2 col-form-label">Email</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="email" name="email" value="<?= $user->email; ?>" readonly>
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-sm-2 col-form-label">Old Password</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Old Password">
						<?= form_error('oldpassword', '<small class="text-danger pl-3">', '</small>'); ?>
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-sm-2 col-form-label">New Password</label>
					<div class="col-sm-5">
						<input type="password" class="form-control" id="password1" name="password1" placeholder="New Password">
						<?= form_error('password1', '<small class="text-danger pl-3">', '</small>'); ?>
					</div>
					<div class="col-sm-5">
						<input type="password" class="form-control" id="password2" name="password2" placeholder="Confirm Password">
					</div>
				</div>
				<div class="form-group row justify-content-end">
					<div class="col-sm-10">
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</div>
			</form>
		</div>
	</div>


</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
