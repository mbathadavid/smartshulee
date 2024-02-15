<?php $settings = $this->ion_auth->settings(); ?>
<div class="col-md-12">

  <div class="widget">

    <div class="blocdk invoice">


      <div class="widget">
        <div class=" container">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6 logo">
                <h1><img src="<?php echo base_url('uploads/files/' . $settings->document); ?>" width="80" height="80" /></h1>
              </div>
              <div class="col-md-6 mt=">
                <h5 class="rtl-text text-center">JUJA prep secondary<?php echo ucwords($settings->school); ?></h5>
              </div>
            </div>
          </div>
        </div>

      </div>


    </div>

  </div>

</div>

<style>
  .rtl-text {
    padding-right: 0px;
    
  }

  @media print {

    .navigation {
      display: none;
    }

    .head {
      display: none;
    }

    .tip {
      display: none !important;
    }

    .bank {
      float: right;
    }

    .view-title h1 {
      border: none !important;
    }

    .view-title h3 {
      border: none !important;
    }

    .split {

      float: left;
    }

    .header {
      display: none
    }

    .invoice {
      width: 100%;
      margin: auto !important;
      padding: 0px !important;
    }

    .invoice table {
      padding-left: 0;
      margin-left: 0;
    }

    .smf .content {
      margin-left: 0px;
    }

    .content {
      margin-left: 0px;
      padding: 0px;
    }
  }

  .logo {
    margin-top: 20px;
  }

  .details {
    margin-right: 1px;
  }
</style>