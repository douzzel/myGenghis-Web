<?php
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use phpOffice\phpSpreadsheet\IOFactory;

    
    class UtilUploads{

        private $file;
        private $active_sheet;
        private $file_name;
        private $allowed;
        private $file_extension;
        private $file_type;
        protected $toto;

        public function __construct(){

           $this->file = new Spreadsheet;
           $this->active_sheet = $this->file->getActiveSheet();
           $this->allowed = array('xls','csv','xlsv');
        }

        public function uploadsExcel($fileName, $fileType){
            $this->file_name = $fileName;
            $file_array = explode(".", $this->file_name);
            $this->file_extension = end($file_array);

            if(in_array($this->file_extension, $this->allowed)){

                $this->file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($fileType);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($this->file_type);
                $spreadsheet = $reader->load($fileType);
                $worksheet = $spreadsheet->getActiveSheet()->toArray(null, true);
                
                return $worksheet;
    


            }



        }

    }

?>
