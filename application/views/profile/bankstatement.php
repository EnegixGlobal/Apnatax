
            <div class="card-body">
                <div class="row <?= $this->session->flashdata('remaining')===NULL?'':'bg-danger py-2'; ?>">
                    <div class="col-md-12">
                        <div class="lead">Add Bank Statement</div>
                        <?= form_open_multipart('profile/savebankstatement'); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= create_form_input('select','month','Month',true,'',['id'=>'month'],month_dropdown($this->session->year)); ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= create_form_input('file','statement','Bank Statement',true,'',['id'=>'statement',' accept'=>"application/pdf"]); ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= create_form_input('file','creditors_statement','Creditors Statement',true,'',['id'=>'creditors_statement',' accept'=>"application/pdf"]); ?>
                                    </div>
                                </div>
                                <div class="col-md-3"><br>
                                    <button type="submit" name="savebankstatement" class="btn btn-success">Upload Bank Statement</button>
                                </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="lead">Monthly Statements</div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <th>Statement</th>
                                        <th>Creditors Statement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(!empty($statements)){ $i=0;
                                        foreach($statements as $single ){
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= $single['year_value']; ?></td>
                                        <td><?= $single['month_value']; ?></td>
                                        <td><a href="<?= $single['statement']?:'#'; ?>" target="_blank" class="btn btn-sm btn-primary">View</a></td>
                                        <td><a href="<?= $single['creditors_statement']?:'#'; ?>" target="_blank" class="btn btn-sm btn-primary">View</a></td>
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
                    
                    <script>
                        $(document).ready(function(e) {
                            $('#datatable').dataTable();
                            $.get( "<?= base_url('franchise/franchisestatus'); ?>", function( data ) {
                            });
                        });

                        function validate(){
                            if($('input[name="sections[]"]:checked').length<1){
                                alert("Please select atleast 1 Section!");
                                return false;
                            }
                        }
                    </script>
