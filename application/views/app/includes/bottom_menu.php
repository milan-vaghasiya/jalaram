<div class="menubar-area style-5 footer-fixed">
	<div class="toolbar-inner menubar-nav">
		<a href="<?=base_url("app/dashboard")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/dashboard')?'active':''?>">
			<div class="shape">
				<i class="fa-solid fa-house"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Home</span>
		</a>
		<a href="<?=base_url("app/salesQuotation")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/salesQuotation')?'active':''?>">
			<div class="shape">
				<i class="far fa-list-alt"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Quotation</span>
		</a>
		<a href="<?=base_url("app/salesOrder")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/salesOrder')?'active':''?>">
			<div class="shape">
				<i class="far fa-file-alt"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Sales</span>
		</a>
		<a href="<?=base_url("app/purchaseOrder")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/purchaseOrder')?'active':''?>">
			<div class="shape">
				<i class="ti-check-box"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Purchase</span>
		</a>
		<a href="<?=base_url("app/VisitorLogs")?>" class="nav-link <?=($this->data['headData']->appMenu == 'app/visitorLogs')?'active':''?>">
			<div class="shape">
				<i class="fas fa-users"></i>
				<div class="inner-shape"></div>
			</div>
			<span>Visitor</span>
		</a>
	</div>
</div>