<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
				<!-- Dashboard -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?=base_url('dashboard')?>" aria-expanded="false">
                        <i class="icon-Car-Wheel"></i><span class="hide-menu">Dashboards </span>
                    </a>
                </li>
                <?=$this->permission->getEmployeeMenus()?>
                
                <?php
                if($this->userRole == 8){
                    ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?=base_url('production_v3/vendorLog/')?>" aria-expanded="false">
                            <i class="ti ti-file-invoice menu-icon"></i><span class="hide-menu">Challan </span>
                        </a>
                    </li>
                    <?php
                }else{
                    ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?=base_url('reportsView')?>" aria-expanded="false">
                            <i class="icon-Bar-Chart"></i><span class="hide-menu">Reports </span>
                        </a>
                    </li>
                    <?php
                    
                }
                ?>
            </ul>
        </nav>
    </div>
</aside>