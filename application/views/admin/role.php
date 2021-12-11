<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

	<div class="row">
		<div class="col-lg-4">

			<?= form_error('role', '<div class="alert alert-danger" role="alert">', '</div>'); ?>

			<?= $this->session->flashdata('message'); ?>

			<a href="" class="btn btn-primary mb-3" data-toggle="modal" data-target="#newRoleModal">Add New Role</a>
			<table class="table table-hover">
				<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col">Role</th>
						<th scope="col">Action</th>
						<th scope="col">Access</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					<?php foreach ($role as $r) : ?>
						<tr>
							<th scope="row"><?= $i; ?></th>
							<td scope="row"><?= $r->role ?></td>
							<td scope="row">
								<a href="" class="badge badge-success">edit</a>
								<a href="" class="badge badge-danger">delete</a>
							</td>
							<td scope="row">
								<a href="" class="badge badge-primary" data-toggle="modal" data-target="#newAccess<?= $r->id; ?>">
									<i class="fas fa-fw fa-cogs"></i>
								</a>
							</td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>


</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Modal -->
<div class="modal fade" id="newRoleModal" tabindex="-1" role="dialog" aria-labelledby="newRoleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="newRoleModalLabel">Add New Role</h5>
				<button class="close" type="button" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form action="<?= base_url('Admin/role'); ?>" method="POST">
				<div class="modal-body">
					<div class="form-group">
						<input type="text" class="form-control" id="role" name="role" placeholder="Role name">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Add</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Access Control-->
<?php foreach ($role as $r) : ?>
	<div class="modal fade" data-keyboard="false" data-backdrop="static" id="newAccess<?= $r->id; ?>" tabindex="-1" role="dialog" aria-labelledby="newAccessLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="newAccessLabel">Add New Role</h5>
					<!-- <button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button> -->
				</div>
				<div class="modal-body">
					<div class="alert alert-success success-alert" id="success-alert">
						<button type="button" class="close" data-dismiss="alert">x</button>
						<strong>Access Changed!</strong>
					</div>
					<table class="table table-hover mt-3">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Menu</th>
								<th scope="col">Access</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php foreach ($menu as $m) : ?>
								<tr>
									<th scope="row"><?= $i; ?></th>
									<td scope="row"><?= $m->menu ?></td>
									<td scope="row">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" <?= check_access($r->id, $m->id); ?> data-role="<?= $r->id; ?>" data-menu="<?= $m->id; ?>">
										</div>
									</td>
								</tr>
								<?php $i++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
					<a href="<?= base_url('Admin/role'); ?>" class="btn btn-secondary" role="button" aria-pressed="true">Cancel</a>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
