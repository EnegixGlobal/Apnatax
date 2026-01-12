            
            <div class="card">
                
                <div class="card-body">
                    <div class="row" id="service-row">
                        <div class="col-md-12 px-5">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Service</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($services)){ $i=0;
                                            foreach($services as $single){
                                                $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info view-btn" value="<?= $single['id'] ?>"><i class="fa fa-eye"></i> Required Documents</button>
                                                <a href="<?= base_url('masterkey/editdocuments/'.md5('service-id-'.$single['id'])); ?>" class="btn btn-sm btn-primary edit-btn"><i class="fa fa-edit"></i></a>
                                            </td>
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
                    <div class="row d-none" id="doc-row">
                        <div class="col-md-12 px-5">
                            <hr>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Upload Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger close-btn">Close</button>
                        </div>
                    </div>
                </div>
            <script>
                $(document).ready(function(e) {
                    $('body').on('click','.view-btn',function(){
                        var service_id=$(this).val();
                        $.ajax({
                            type:"post",
                            url:"<?= base_url('masterkey/getservicedocuments'); ?>",
                            data:{service_id:service_id},
                            success:function(data){
                                data=JSON.parse(data);
                                var tableBody = $('#doc-row table tbody');
                                tableBody.html('');
                                var sl=0;
                                
                                for(var i in data){
                                    var type=[];
                                    sl++;
                                    // Create a new row and append it to the table body
                                    var newRow = $('<tr>');
                                    newRow.append('<td>'+sl+'</td>');
                                    newRow.append('<td>'+data[i]['display_name']+'</td>');
                                    newRow.append('<td>'+data[i]['type']+'</td>');
                                    newRow.append('<td>'+data[i]['file_type']+'</td>');

                                    // Append the new row to the table body
                                    tableBody.append(newRow);
                                }
                                $('#doc-row').removeClass('d-none');
                            }
                        });
                    });
                    $('body').on('click','.close-btn',function(){
                        $('#doc-row').addClass('d-none');
                    });
                    $('#table').dataTable();
                });
            function getPhoto(input){

            }
            </script>
            </div>