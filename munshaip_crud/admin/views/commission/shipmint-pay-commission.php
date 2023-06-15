<div class="container wpowt-lib-cont">
    
  <?php //print_r($params);die; 
  //print_r($params['action']);die;
//print_r($params['commission_data']);die;
  ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                <?php
                if (isset($params['action']) && $params['action'] == "edit") {
                    echo 'Edit Commission';
                } elseif (isset($params['action']) && $params['action'] == "view") {
                    echo 'View Commission';
                } else {
                    echo 'Create Pay Commission';
                }
                ?>
                <a href="admin.php?page=shipmint-manage-commission" class="btn btn-danger pull-right owt-btn-right owt-btn-top wpowt-lib-btn "><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a>
            </div>
            <div class="panel-body">

                <form action="javascript:void(0)" id="wpowt-lib-frm-create-new-student" method="post">
                    <div class="col-sm-8">
                        <div class="form-group">
                            
                            <input type="hidden" required="" <?php
                                    if (isset($params['action']) && $params['action'] == "edit") {
                                                                echo 'readonly';

                                                            } else {
                                                            }
                                                            ?> value="<?php echo isset($params['commission_data']['driver_id']) ? esc_attr($params['commission_data']['driver_id']) : ""; ?>" class="form-control" id="txt-reg-id" name="txt_reg_id" placeholder="Enter registration ID">

                        </div>
                        <div class="form-group">
                            <label for="txtname">Partner Name</label>
                            <input type="text" required  value="<?php echo isset($params['commission_data']['name']) ? esc_attr($params['commission_data']['name']) : ""; ?>" class="form-control" id="partner_name" name="partner_name" placeholder="Enter Partner Name">
                        </div>



                        <div class="form-group">
                            <label for="txtname">Total Commission</label>
                            <input type="text" required   id="tot_comm" value="<?php echo isset($params['commission_data']['total_commission']) ? esc_attr($params['commission_data']['total_commission']) : ""; ?>" class="form-control" name="tot_comm" placeholder="Enter Total Commission">
                        </div>
                        <div class="form-group">
                            <label for="txtname">Pay Commission</label>
                            <input type="text" required class="form-control" id="pay_comm" value="<?php echo isset($params['commission_data']['pay_commission']) ? esc_attr($params['commission_data']['pay_commission']) : ""; ?>" name="pay_comm" placeholder="Enter Pay Commission">
                        </div>
                        <div class="form-group">
                            <label for="txtname">Available Commission</label>
                            <input type="text" required   id="avil_comm" value="<?php echo isset($params['commission_data']['avil_commission']) ? esc_attr($params['commission_data']['avil_commission']) : ""; ?>" class="form-control" name="avil_comm" placeholder="Enter Available">
                        </div>
                        <div class="form-group">
                            <label for="txtname">Pay By</label>
                            <input type="text" required class="form-control" id="pay_by" value="<?php echo isset($params['commission_data']['pay_by']) ? esc_attr($params['commission_data']['pay_by']) : ""; ?>" name="pay_by" placeholder="Enter Payment By">
                        </div>
                        <div class="form-group">
                            <label for="txtname">Referance Number</label>
                            <input type="text" required class="form-control" id="ref_no" value="<?php echo isset($params['commission_data']['referance_no']) ? esc_attr($params['commission_data']['referance_no']) : ""; ?>" name="ref_no" placeholder="Enter Referance No">
                        </div>
                        

                      </div>
                       
                  
                    <?php if (isset($params['action']) && $params['action'] != "view") { ?>
                        <div class="form-group">
                            <input type="hidden" name="opt_action" value="<?php echo $params['action']; ?>" />
                            <div class="col-sm-12 owt-text-align">
                                <button type="submit" class="btn btn-success wpowt-lib-btn"><i class="mdi mdi-check-outline"></i> Submit</button>
                            </div>
                        </div>
                    <?php } ?>
                </form>

            </div>

        </div>

    </div>
</div>