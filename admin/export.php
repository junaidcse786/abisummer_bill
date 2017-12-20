<?php 

require_once('../config/dbconnect.php');

if(!isset($_SESSION["admin_panel"])){
		
    header('Location: '.SITE_URL_ADMIN.'login.php');
}

function export($table, $db){

    $query = mysqli_query($db, "SHOW COLUMNS FROM $table");    

    if(count($query) > 0){
        
        $delimiter = ",";
        
        $filename = $table . date('Y-m-d') . ".csv";

        $f = fopen('php://memory', 'w');

        $fields = array();
        
        while($row = mysqli_fetch_object($query))
            
            $fields[]=$row->Field;
        
        fputcsv($f, $fields, $delimiter);
        
        $query_records = mysqli_query($db, "SELECT * FROM $table");
        
        

        while($row_fields = mysqli_fetch_array($query_records)){
         
              foreach($fields as $val)
                  
                  $acc_records[]= $row_fields[$val];
              
              fputcsv($f, $acc_records, $delimiter);
            
              unset($acc_records); $acc_records = array();
        }
                
        fseek($f, 0);

        header('Content-Type: text/csv');
        
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        fpassthru($f);
    }
    
}

if(isset($_POST["submit_export"])){    

    export($db_suffix.$_POST["table_name"], $db);
    
}

?>