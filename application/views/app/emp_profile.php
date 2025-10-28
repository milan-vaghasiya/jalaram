<?php $this->load->view('app/includes/header'); ?>
	
	<!-- Header -->
	<header class="header">
		<div class="main-bar">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="back-btn">
							<svg height="512" viewBox="0 0 486.65 486.65" width="512"><path d="m202.114 444.648c-8.01-.114-15.65-3.388-21.257-9.11l-171.875-171.572c-11.907-11.81-11.986-31.037-.176-42.945.058-.059.117-.118.176-.176l171.876-171.571c12.738-10.909 31.908-9.426 42.817 3.313 9.736 11.369 9.736 28.136 0 39.504l-150.315 150.315 151.833 150.315c11.774 11.844 11.774 30.973 0 42.817-6.045 6.184-14.439 9.498-23.079 9.11z"/><path d="m456.283 272.773h-425.133c-16.771 0-30.367-13.596-30.367-30.367s13.596-30.367 30.367-30.367h425.133c16.771 0 30.367 13.596 30.367 30.367s-13.596 30.367-30.367 30.367z"/>
							</svg>
						</a>
						<h5 class="title mb-0 text-nowrap">Profile</h5>
					</div>
					<div class="mid-content">
					</div>
					<div class="right-content">
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    <?php
    $profile_pic = 'male_user.png';
    if(!empty($empData->emp_profile)):
        $profile_pic = $empData->emp_profile;
    else:
        if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
            $profile_pic = 'female_user.png';
        endif;
    endif;
    ?>
    <!-- Page Content -->
    <div class="page-content bottom-content">
        <div class="container">
			<div class="driver-profile">
				<div class="media media-100 mb-2">
					<img class="rounded-circle" src="<?= base_url('assets/uploads/emp_profile/'.$profile_pic) ?>" alt="driver-image">
				</div>
				<div class="profile-detail">
					<h6 class="name mb-0 font-18"><?=$empData->emp_name?></h6>
					<span class="text-center d-block"><?=$empData->designation?></span>
				</div>
			</div>
			
			<div class="dz-list">
				<ul>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa fa-user"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_name?></span>
							</div>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa fa-at"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_email?></span>
							</div>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" class="item-content">
							<div class="dz-icon">
								<i class="fa-solid fa-phone"></i>
							</div>
							<div class="dz-inner">
								<span class="title"><?=$empData->emp_contact?></span>
							</div>
						</a>
					</li>
					
				</ul>
			</div>
		</div>
    </div>    
    <!-- Page Content End-->
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>