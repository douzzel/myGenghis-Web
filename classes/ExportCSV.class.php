<?php
    class ExportCSV {
        public static function Export($db_record, $filename = 'export', $attachment = false, $headers = true, $where = null){
            if($attachment) {
                header( 'Content-Type: text/csv' );
                header( 'Content-Disposition: attachment;filename='.$filename.'.csv');
                $fp = fopen('php://output', 'w');
            } else {
                $fp = fopen($filename, 'w');
            }

            $where2 = isset($where) ? 'WHERE '.$where : '';
           
            $result = MYSQL::query('SELECT * FROM '.$db_record.' '.$where2);
           
            if($headers) {
                // output header row (if at least one row exists)
                $row = mysqli_fetch_assoc($result);
                if($row) {
                    fputcsv($fp, array_keys($row));
                    // reset pointer back to beginning
                    mysqli_data_seek($result, 0);
                }
            }
           
            while($row = mysqli_fetch_assoc($result)) {
                fputcsv($fp, $row);
            }
           
            fclose($fp);

            exit;
        }

        public static function create(){
            $file = 'test.csv';
            $table = 'table_name';

            // get structure from csv and insert db
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen($file,'r');

            // first row, structure
            if ( ($data = fgetcsv($handle) ) === FALSE ) {
                echo "Cannot read from csv $file"; die();
            }

            $fields = array();
            $field_count = 0;
            for($i=0;$i<count($data); $i++) {
                $f = strtolower(trim($data[$i]));
                if ($f) {
                    $f = substr(preg_replace ('/[^0-9a-z]/', '_', $f), 0, 20);
                    $field_count++;
                    $fields[] = $f.' VARCHAR(50)';
                }
            }

            $sql = "CREATE TABLE $table (" . implode(', ', $fields) . ')';
            MYSQL::query($sql);
            while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                $fields = array();
                for($i=0;$i<$field_count; $i++) {
                    $fields[] = '\''.addslashes($data[$i]).'\'';
                }
                $sql = "Insert into $table values(" . implode(', ', $fields) . ')';
                echo $sql; 
                MYSQL::query($sql);
            }
            fclose($handle);
            ini_set('auto_detect_line_endings',FALSE);
        }
    }
?>
