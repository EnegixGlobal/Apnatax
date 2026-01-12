

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 200px">Service</th>
                                <?php 
                                    if(empty($years)){
                                        if(date('d')<=31 && date('m')<4){
                                            $start=(date('Y')-1).'-04-01';
                                        }
                                        else{
                                            $start=date('Y').'-04-01';
                                        }
                                    }
                                    else{
                                        $start=date($years['year1'].'-04-01');
                                    }
                                    for($i=0;$i<12;$i++){
                                ?>
                                <th class="months"><?= date('F-Y',strtotime($start." +$i month")); ?></th>
                                <?php } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($purchases)){
                                $service_ids=array_column($purchases,'service_id');
                                $dates=array_column($purchases,'date');
                                //print_pre($service_ids);
                                //print_pre($dates);
                            }
                            $total_amount=0;
                            if(!empty($services)){
                                foreach($services as $single){
                                    //echo $single['name'];
                                    $filteredDates=array();
                                    $indices = !empty($purchases)?array_keys($service_ids, $single['id']):array();
                                    if(!empty($indices)){
                                        foreach($indices as $index){
                                            $filteredDates[$index]=$dates[$index];
                                        }
                                    }
                                    //print_pre($filteredDates);
                                    $total=0;
                            ?>
                            <tr>
                                <td><?= $single['name']; ?></td>
                                <?php
                                for($i=0;$i<12;$i++){
                                    if(empty($month[$i])){
                                        $month[$i]=0;
                                    }
                                    $text='';
                                    if(!empty($filteredDates)){
                                        //echo date('F-Y',strtotime($start." +$i month"));
                                        $first=date('Y-m-01',strtotime($start." +$i month"));
                                        $last=date('Y-m-t',strtotime($start." +$i month"));
                                        $searches=findDateIndices($filteredDates,$first,$last);
                                        if(!empty($searches)){
                                            $text=0;
                                            foreach($searches as $index){
                                                $text+=$purchases[$index]['amount'];
                                            }
                                            $total+=$text;
                                            $month[$i]+=$text;
                                            $text=$this->amount->toDecimal($text,false);
                                        }
                                    }
                                ?>
                                <td><?= $text; ?></td>
                                <?php } ?>
                                <td><?= $this->amount->toDecimal($total,false); ?></td>
                            </tr>
                            <?php
                                    $total_amount+=$total;
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th> Total</th>
                                <?php
                                for($i=0;$i<12;$i++){
                                ?>
                                <th><?= !empty($month[$i])?$this->amount->toDecimal($month[$i],false):''; ?></th>
                                <?php
                                }
                                ?>
                                <th><?= $this->amount->toDecimal($total_amount,false); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>