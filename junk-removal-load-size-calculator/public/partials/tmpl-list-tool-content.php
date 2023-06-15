<div class="row" style="margin-top:20px;">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="text-center"><?php esc_html_e( 'Know Your Estimation', 'jrlsc-domain' ); ?></h3>
            </div>
            <div class="panel-body">
                <form  action="javascript:void(0)" class="clshowhide" id="frm-add-estimate">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fname" data-toggle="tooltip"  title="Enter First Name"><?php esc_html_e( 'First Name:', 'jrlsc-domain' ); ?></label>
                            <input  type="text" class="form-control" id="fname" name="fname" placeholder="Enter First Name" required >
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lname" data-toggle="tooltip"  title="Enter Last Name"><?php esc_html_e( 'Last Name:', 'jrlsc-domain' ); ?></label>
                            <input type="text" class="form-control" id="lname"  name="lname" placeholder="Enter Last Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="calcxl" data-toggle="tooltip"  title="Enter XL"><?php esc_html_e( 'XL:', 'jrlsc-domain' ); ?></label>
                            <input type="number" name="calcxl" value="0" class="form-control xl_calc"  id="calcxl" placeholder="Enter XL" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="calcl" data-toggle="tooltip"  title='Enter L'><?php esc_html_e( 'L:', 'jrlsc-domain' ); ?></label>
                            <input type="number" name="calcl" value="0" class="form-control xl_calc"  id="calcl" placeholder="Enter L" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="calcm" data-toggle="tooltip"  title='Enter M'><?php esc_html_e( 'M:', 'jrlsc-domain' ); ?></label>
                            <input type="number" name="calcm" value="0" class="form-control xl_calc"  id="calcm" placeholder="Enter M" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="calcrbb" data-toggle="tooltip"  title='Enter Recyle/ Bags/ Box'><?php esc_html_e( 'Recyle/ Bags/ Box:', 'jrlsc-domain' ); ?></label>
                            <input type="number" name="calcrbb" value="0" class="form-control xl_calc"  id="calcrbb" placeholder="Enter Recyle" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="hidden" value="" class="form-control" name="truck" id="truck" >
                        </div>
                        <div class="form-group col-md-6">
                            <input type="hidden"  value="0.00"  class="form-control" name="txtTotalAmount" id="txtTotalAmount">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12 valhideshow" >
                            <?php
                            $data = get_option( 'jrls_calc_get_a_quote_options' );
                            $dataurl = !empty($data['input_example'])? esc_html($data['input_example']) : site_url();
                            $btnlbl = get_option( 'jrls_calc_input_label_options' );
                            $btnlabel =!empty($btnlbl['input_label'])? esc_html($btnlbl['input_label']) : __( "Click here for a price quote", "jrlsc-domain" );
                            ?>
                            <p><?php esc_html_e( 'This pickup would be a', 'jrlsc-domain' ); ?> <strong><span id="rs_truck"></span></strong> <?php esc_html_e('Load. Junk removal costs are based on volume and measured in truck space. This is for estimation purposes only. We’ll confirm the quote in person and before starting any work. ', 'jrlsc-domain' ); ?></p>
                            <p><a href="<?php echo $dataurl; ?>" target="_blank" id="btn-front-end-ajax" class="btn-calc button"><?php echo $btnlabel; ?></a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
