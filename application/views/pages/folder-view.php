            
            <div class="card">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">

                            <div class="folder-container">
                                <?php
                                    if(!empty($folders)){
                                        foreach($folders as $folder){
                                            $icon='<i class="fa fa-folder closed"></i>
                                            <i class="fa fa-folder-open open"></i>';
                                            if(!empty($folder['type']) && $folder['type']=='add'){
                                                $icon='<i class="fa fa-folder-plus closed"></i>
                                                <i class="fa fa-folder-plus open"></i>';
                                            }
                                ?>
                                <div class="folder-doc">
                                    <?php /*?><a href="#" class="btn btn-sm p-0 folder-edit"><i class="fa fa-edit"></i></a><?php */?>
                                    <a href="<?= base_url($folder['link']); ?>" class="folder">
                                        <div class="folder-icon">
                                            <?= $icon; ?>
                                        </div>
                                        <div class="folder-name"><?= $folder['name']; ?></div>
                                        <?php
                                            if(empty($folder['type'])){
                                        ?>
                                        <div class="folder-count"><?= $folder['count']; ?> <?= $folder['count_text']??'items'; ?></div>
                                        <?php
                                            }
                                            if(!empty($folder['popup_details'])){
                                        ?>
                                        <!-- Popup Details -->
                                        <div class="folder-popup">
                                            <div class="popup-title"><?= $folder['name']; ?></div>
                                            <div class="popup-details">
                                                <?php
                                                foreach($folder['popup_details'] as $single){
                                                ?>
                                                <div><i class="<?= $single['icon']; ?>"></i> <?= $single['text']; ?></div>
                                                <?php
                                                }
                                                ?>
                                                <?php /*?><div><i class="fa fa-file-alt"></i> 8 PDFs</div>
                                                <div><i class="fa fa-file-word"></i> 4 DOCs</div>
                                                <div>Last modified: Today</div>
                                                <div>Size: 24.5 MB</div><?php */?>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                    </a>
                                </div>
                                <?php
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
<script>
    $(document).ready(function(){
        var prev=0;
        $('.folder').each(function(){
            var fTop=$(this).offset().top;
            if(prev==0 || prev==fTop){
                $(this).find('.folder-popup').addClass('bottom');
                prev=fTop;
            }
            else{
                $(this).find('.folder-popup').addClass('top');
                prev=-1;
            }
            
        });
    });
</script>