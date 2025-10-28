<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title text-center">Auto Mail</h4>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="addAutoMail">
                        <div class="table-responsive">
                            <table id='autoMailTable' class="table table-bordered ssTable" data-url='/getAutoMailRows'></table>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="store('addAutoMail','saveAutoMail',0);">SAVE AUTO MAIL</button>
</div>
<?php $this->load->view('includes/footer'); ?>