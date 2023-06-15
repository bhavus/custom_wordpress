<div class="row" style="margin-top:20px;">
    <div class="col-sm-10">
        <div class="panel panel-primary">
            <div class="panel-heading"><?php esc_html_e( 'List Estimation', 'jrlsc-domain' ); ?></div>
            <div class="panel-body">
                <table id="tbl-list-jrlsc-shelf" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( '#ID', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'First Name', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'Last Name', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'XL', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'L', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'M', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'Recyle/Bags/Box', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'Truck', 'jrlsc-domain' ); ?></th>
                            <th><?php esc_html_e( 'Approx. Estimation', 'jrlsc-domain' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $wpdb;
                        $jrlsc_shelf = $wpdb->get_results("SELECT * FROM ".JRLS_PREFIX_TBL);

                        if( !empty( $jrlsc_shelf ) && count($jrlsc_shelf) > 0 ){

                            foreach( $jrlsc_shelf as $index => $data ){
                                ?>
                                <tr>
                                    <td><?php echo !empty( $data->id ) ? $data->id : ''; ?></td>
                                    <td><?php echo !empty( $data->fname ) ? $data->fname : ''; ?></td>
                                    <td><?php echo !empty( $data->lname ) ? $data->lname : ''; ?></td>
                                    <td><?php echo !empty( $data->calcxl ) ? $data->calcxl : 0; ?></td>
                                    <td><?php echo !empty( $data->calcl ) ? $data->calcl : 0; ?></td>
                                    <td><?php echo !empty( $data->calcm ) ? $data->calcm : 0; ?></td>
                                    <td><?php echo !empty( $data->calcrbb ) ? $data->calcrbb : 0; ?></td>
                                    <td><?php echo !empty( $data->truck ) ? $data->truck : ''; ?></td>
                                    <td><?php echo !empty( $data->txtTotalAmount ) ? $data->txtTotalAmount : ''; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>