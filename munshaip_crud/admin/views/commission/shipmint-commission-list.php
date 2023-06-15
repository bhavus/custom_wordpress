<div class="container">
    
   <?php //print_r($params['commissions']); die; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
            Commission List
                <a href="admin.php?page=shipmint-add-commission" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">Add Commission</a>

               
                
                
            </div>
            <div class="panel-body">
                <table id="owt-tbl-book-list" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner Name</th>
                            <th>Total Commission</th>
                            <th>Pay Commission</th>
                            <th>Available Commission</th>
                            <th>Pay By</th>
                            <th>Referance Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($params['commissions'])) {

                            if (count($params['commissions']) > 0) {

                                $count = 1;
                                foreach ($params['commissions'] as $commission) {
                                    ?>
                                    <tr>
                                        <td><?php echo $commission->driver_id; ?></td>
                                        <td><?php echo ucfirst($commission->name); ?></td>
                                        <td><?php echo $commission->total_commission; ?></td>
                                        <td><?php echo $commission->pay_commission; ?></td>
                                        <td><?php echo $commission->avil_commission; ?></td>
                                        <td><?php echo $commission->pay_by; ?></td>
                                        <td><?php echo $commission->referance_no; ?></td>
                                        
                                        
                                        <td>
                                            <a href="admin.php?page=shipmint-add-commission&action=edit&stid=<?php echo $commission->id; ?>" class='btn btn-info wpowt-lib-btn' title="Edit"><i class="mdi mdi-pencil"></i></a>
                                            <a href="admin.php?page=shipmint-add-commission&action=view&stid=<?php echo $commission->id; ?>" class='btn btn-info wpowt-lib-btn' title="View"><i class="mdi mdi-eye"></i></a>
                                            <a href="javascript:void(0)" class='btn btn-danger wpowt-lib-del-student wpowt-lib-btn' data-id="<?php echo $commission->id; ?>" title="Delete"><i class="mdi mdi-trash-can-outline"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </tbody>
                </table> 
            </div>
        </div>

    </div>
</div>

