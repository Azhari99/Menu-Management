<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

	<!-- Sidebar - Brand -->
	<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
		<div class="sidebar-brand-icon rotate-n-15">
			<i class="fas fa-code"></i>
		</div>
		<div class="sidebar-brand-text mx-3">Wpu Admin</div>
	</a>

	<!-- Divider -->
	<hr class="sidebar-divider">

	<!-- Query Menu -->
	<?php
	$role_id = $this->session->userdata('role_id');
	$queryMenu = "SELECT m.id, m.menu FROM user_menu m
				JOIN user_access_menu am ON m.id = am.menu_id 
				WHERE am.role_id = $role_id
				ORDER BY am.menu_id ASC";

	$menu = $this->db->query($queryMenu)->result();
	?>

	<!-- Looping menu -->
	<?php foreach ($menu as $m) : ?>
		<div class="sidebar-heading">
			<?= $m->menu; ?>
		</div>

		<!-- Sub-Menu sesui Menu -->
		<?php
		$menu_id = $m->id;
		$querySubMenu = "SELECT * FROM user_sub_menu sm 
						JOIN user_menu m ON sm.menu_id = m.id
						WHERE sm.menu_id = $menu_id AND sm.is_active = 1";

		$subMenu = $this->db->query($querySubMenu)->result();
		?>

		<?php foreach ($subMenu as $sm) : ?>
			<?php if ($title == $sm->title) : ?>
				<li class="nav-item active">
				<?php else : ?>
				<li class="nav-item">
				<?php endif; ?>
				<a class="nav-link pb-0" href="<?= base_url($sm->url) ?>">
					<i class="<?= $sm->icon ?>"></i>
					<span><?= $sm->title ?></span></a>
				</li>
				<!-- Divider -->

			<?php endforeach; ?>

			<hr class="sidebar-divider mt-3">

		<?php endforeach; ?>

		<li class="nav-item">
			<a class="nav-link pt-0" href="#" data-toggle="modal" data-target="#logoutModal">
				<i class="fas fa-fw fa-sign-out-alt"></i>
				<span>Logout</span></a>
		</li>

		<!-- Divider -->
		<hr class="sidebar-divider d-none d-md-block">

		<!-- Sidebar Toggler (Sidebar) -->
		<div class="text-center d-none d-md-inline">
			<button class="rounded-circle border-0" id="sidebarToggle"></button>
		</div>

</ul>
<!-- End of Sidebar -->
