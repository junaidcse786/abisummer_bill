<?php 

require_once('../config/dbconnect.php');

if(!isset($_SESSION["admin_panel"])){
		
    header('Location: '.SITE_URL_ADMIN.'login.php');
}

function import($table, $filename, $db){

    $row=1; 

    $query="INSERT INTO $table (";

    if (($handle = fopen($filename, "r")) !== FALSE) {        

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {            

            foreach($data as $each_cell){

                if($row>1)

                    $query.='"'.$each_cell.'",';

                else

                    $query.=''.$each_cell.',';
                
                unset($each_cell);
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

        $query=rtrim($query, ', (');
        
        if(mysqli_query($db, $query))
            
            echo "<script type='text/javascript'>alert('Imported successfully!')</script>";
        
        else
            
            echo "<script type='text/javascript'>alert('Import failed!')</script>".$query;
    }

    fclose($handle);
}

if(isset($_POST["submit"]) && !empty($_POST["table_name"]) && !empty($_FILES["file_name"]["tmp_name"])){    

    import($db_suffix.$_POST["table_name"], $_FILES["file_name"]["tmp_name"], $db);
    
}

?>
<html>
<head>
</head>
    <body>
        <div style="width:30%; margin: 0 auto; padding: 15%;">
            <form action="import.php" method="post" enctype="multipart/form-data">
                <h1>Import</h3>
                <input autofocus autocomplete="off" placeholder="Table name" type="text" name="table_name"><br/><br/>
                <input type="file" name="file_name"><br/><br/>
                <button type="submit" name="submit">Submit</button>
            </form>
            <br/><br/><br/>
            <form action="export.php" method="post" enctype="multipart/form-data">
                <h1>Export</h3>
                <input autocomplete="off" placeholder="Table name" type="text" name="table_name"><br/><br/>
                <button type="submit" name="submit_export">Submit</button>
            </form>
        </div>
    </body>
</html>