<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;

	if(!function_exists('openexcelfile')) {
  		function openexcelfile($filepath) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $spreadsheet = $reader->load($filepath);

            $d=$spreadsheet->getSheet(0)->toArray();

            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $i=1;

            //unset($sheetData[0]);
            $data=array();
            foreach ($sheetData as $t) {
             // process element here;
            // access column by index
                $data[]=$t;
            }
            return $data;
		}  
	}

	if(!function_exists('opencsvfile')) {
  		function opencsvfile($filepath) {
            /*$file = fopen($filepath, "r");

            //Output lines until EOF is reached
            $data=array();
            while(! feof($file)) {
                $line = fgets($file);
                $single=explode(';',$line);
                $data[]=$single;
            }*/
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            //$reader->setDelimiter(',');
            $reader->setEnclosure(' ');
            $reader->setSheetIndex(0);
            
            $spreadsheet = $reader->load($filepath);


            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $i=1;

            //unset($sheetData[0]);
            $data=array();
            foreach ($sheetData as $t) {
             // process element here;
            // access column by index
                $t=array_map("trimQuotes",$t);
                $data[]=$t;
            }
            return $data;
		}  
	}

	if(!function_exists('trimQuotes')) {
        function trimQuotes($v){
          return !empty($v)?trim($v,'"'):$v;
        }
    }


?>
