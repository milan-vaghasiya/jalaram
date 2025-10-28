<!-- Sidebar -->
<div class="dark-overlay"></div>
	<div class="sidebar style-2">
		<a href="javascript:void(0)" class="side-menu-logo">
			<img src="<?=base_url("assets/app/images/logo_text.png")?>" alt="logo" >
		</a>
		<ul class="nav navbar-nav" data-simplebar>	
			<li class="nav-label">Main Menu</li>
			<li>
				<a class="nav-link" href="<?=base_url("app/dashboard")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/dashboard')?'active':''?>">
					<span class="dz-icon">
						<i class="fa-solid fa-house"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Home</span>
				</a>
			</li>
			<li>
				<a href="<?=base_url("app/employee")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/employee')?'active':''?>">
					<span class="dz-icon">
						<i class="fas fa-user-cog"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Profile</span>
				</a>
			</li>
			<li>
				<a href="<?=base_url("app/salesQuotation")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/salesQuotation')?'active':''?>">
					<span class="dz-icon">
						<i class="far fa-list-alt"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Sales Quotation</span>
				</a>
			</li>
			<li>
				<a href="<?=base_url("app/salesOrder")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/salesOrder')?'active':''?>">
					<span class="dz-icon">
						<i class="far fa-file-alt"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Sales Order</span>
				</a>
			</li>
			<li>
				<a href="<?=base_url("app/purchaseOrder")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/purchaseOrder')?'active':''?>">
					<span class="dz-icon">
						<i class="ti-check-box"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Purchase Order</span>
				</a>
			</li>
			<li>
				<a href="<?=base_url("app/VisitorLogs")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/VisitorLogs')?'active':''?>">
					<span class="dz-icon">
						<i class="fas fa-users"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Visitor</span>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?=base_url('app/login/logout')?>">
					<span class="dz-icon">
						<i class="fas fa-sign-out-alt"></i>
						<div class="inner-shape"></div>
					</span>
					<span>Logout</span>
				</a>
			</li>
            
		</ul>
		
    </div>
    <!-- Sidebar End -->