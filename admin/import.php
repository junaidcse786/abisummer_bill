<?php 

require_once('../config/dbconnect.php');

if(!isset($_SESSION["admin_panel"])){
		
    header('Location: '.SITE_URL_ADMIN.'login.php');
}

function import($table, $filename){

    $row=1; 

    $query="INSERT INTO $table (";

    if (($handle = fopen($filename, "r")) !== FALSE) {        

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {            

            foreach($data as $each_cell){

                if($row>1)

                    $query.='"'.$each_cell.'",';

                else

                    $query.=''.$each_cell.',';
            }

            $query=rtrim($query, ',');

            if($row==1){

                $query.=") VALUES (";

                $row++;
            }
            else{

                $query.="), (";
            }

        }

        echo $query=rtrim($query, ', (');
    }

    fclose($handle);
}

if(isset($_POST["submit"])){    

    import($db_suffix.$_POST["table_name"], $_FILES["file_name"]["tmp_name"]);
    
}

?>
<html>
<head>
</head>
    <body>
        <form action="import.php" method="post" enctype="multipart/form-data">
            <input type="text" name="table_name"><br/><br/>
            <input type="file" name="file_name"><br/><br/>
            <button type="submit" name="submit">Submit</button>
        </form>
    </body>
</html>