            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Services</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($services)){ $i=0;
                                            foreach($services as $single){
                                                $i++;
                                                $single=checkservicepurchase($single,$user,$this->session->firm);
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $single['name']; ?></td>
                                            <td><?= $single['rate']; ?></td>
                                            <td>
                                                <?php
                                                if($single['buy_status']===true){
                                                ?>
                                                <button type="button" class="btn btn-sm btn-success buy-btn" value="<?= $single['id'] ?>" data-types="<?= $single['type']; ?>"><i class="fa fa-shopping-cart"></i> Purchase</button>
                                                <?php
                                                }
                                                elseif($single['buy_status']===false){
                                                    echo '<span class="text-danger">'.$single['message'].'</span>';
                                                }
                                                ?>
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
                </div>
                
                <div class="modal  fade" id="typemodal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" id="type-form">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <?= create_form_input('select','','Type',false,'',['id'=>'type'],[''=>'Select Type']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="purchase">Purchase</button>
                            </div>
                        </div>
                        <div class="modal-content d-none" id="package-form">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <?php
                                            $packages=$this->master->getpackages();
                                        ?>
                                        <select name="package_id" id="package_id" class="form-control">
                                            <option value="">Select Package</option>
                                            <option value="<?= generate_slug('Accountancy Prime'); ?>">Accountancy Prime</option>
                                            <option value="<?= generate_slug('Accountancy Premium'); ?>">Accountancy Premium</option>
                                        </select>
                                        <div class="form-group">
                                            <?= create_form_input('number','amount','Monthly Amount',false,'',['id'=>'amount']); ?>
                                        </div>
                                        <table class="table table-bordered d-none" id="package-table">
                                            <thead>
                                                <tr>
                                                    <th>Turnover</th>
                                                    <th>Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(!empty($packages)){
                                                        foreach($packages as $package){
                                                ?>
                                                <tr class="<?= generate_slug($package['name']); ?> package">
                                                    <td><?= $package['remarks']; ?></td>
                                                    <td><?= $package['rate']; ?></td>
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
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="purchase-package">Purchase</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="id">
            <script>
                var myModal;
                $(document).ready(function(e) {
                    myModal = new bootstrap.Modal(document.getElementById('typemodal'));
                    $('table').on('click','.buy-btn',function(){
                        if($(this).val()==1){
                           $('#purchase').text('Select Package');
                        }
                        else{
                           $('#purchase').text('Purchase');
                        }
                        $('#package_id').val('');
                        $('.modal-dialog').removeClass('modal-md').addClass('modal-sm')
                        $('#id').val($(this).val());
                        $('#type-form').removeClass('d-none');
                        $('#package-form').addClass('d-none');
                        var type=$(this).data('types');
                        if(type==''){
                            
                        }
                        else if(type.search(',')==-1){
                            $('#type').html('<option value="">Select Type</option><option value="'+type+'">'+type+'</option>');
                            $('#type').val(type);
                        }
                        else{
                            var types=type.split(',');
                            var options='<option value="">Select Type</option>';
                            for(let i=0;i<types.length;i++){
                                options+='<option value="'+types[i]+'">'+types[i]+'</option>';
                            }
                            $('#type').html(options);
                            
                            myModal.show();
                            return false;
                        }
                        
                        buypackage();
                    });
                    $('body').on('click','#purchase',function(){
                        if($('#type').val()==''){
                            alert('Select type!');
                            return false;
                        }
                        if($('#id').val()==1 && $('#package_id').val()==''){
                            $('#type-form').addClass('d-none');
                            $('#package-table').addClass('d-none');
                            $('#package-form').removeClass('d-none');
                            if($('#type').val()=='Monthly'){
                                $('#amount').parent().removeClass('d-none');
                            }
                            else{
                                $('#amount').parent().addClass('d-none');
                            }
                            $('.modal-dialog').removeClass('modal-sm').addClass('modal-md');
                            
                            return false;
                        }
                        buypackage();
                    });
                    $('body').on('click','#purchase-package',function(){
                        if($('#package_id').val()==''){
                            alert('Select Package!');
                            return false;
                        }
                        if($('#type').val()=='Monthly' && $('#amount').val()==''){
                            alert('Enter Monthly Debit Amount!');
                            return false;
                        }
                        
                        buypackage();
                    });
                    $('body').on('change','#package_id',function(){
                        $('.package').hide();
                        $('.'+$(this).val()).show();
                        $('#package-table').removeClass('d-none');
                    });
                    $('#table').dataTable();
                });
                
                function buypackage(){
                    var id=$('#id').val();
                    var amount=$('#amount').val();
                    $.ajax({
                        type:"post",
                        url:"<?= base_url('services/buyservice/'); ?>",
                        data:{id:id,type:$('#type').val(),amount:amount,package_id:$('#package_id').val()},
                        success:function(data){
                            if(data!=''){
                                window.location=data;
                            }
                            else{
                                window.location.reload();
                            }
                        }
                    });
                }
                
            function validate(){

              return true; // Allow form submiss

            }
            </script>
            </div>