<!DOCTYPE html>
<html>
    <head>
        <title>
            Water Treatment Centre
        </title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="./css/x-admin.css" media="all">
        <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="lib/bootstrap/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
        <script src="lib/layui/layui.js" charset="utf-8"></script>
    </head>
    <body>
        <div class="x-nav">
            <span class="layui-breadcrumb">
              <a><cite>Raw Data</cite></a>
              <a><cite>Water Treatment Centre</cite></a>
            </span>
        </div>
        <div class="x-body">
          <div tabindex="300" class="btn btn-info btn-lg btn-file" >
                <i class="fa fa-folder-open"></i>  
                <span class="hidden-xs">Import …</span>
                <input id="file-3" type="file" onchange="import_data(this.files);">
            </div>
            <button type="button" class="btn btn-info btn-lg" onclick="delete_table();"><i class="layui-icon">&#xe640;</i>Delete Table</button>
            <button type="button" class="btn btn-info btn-lg" onclick="download_table();"><i class="layui-icon">&#xe601;&nbsp;</i>Download</button>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>wtc_id</th>
                        <th>wtc_name</th>
                        <th>belong_catchment_id</th>
                        <th>served_towns</th>
                        <th>served_population</th>
                        <th>annual_water_use</th>
                        <th>health_based_target_index</th>
                        <th>waste_water_quality_index</th>
                        <th>water_supply_deficiency_index</th>
                    <tr>
                </thead>
                <tbody>
                <?php
                    include 'db.helper/db_connection_ini.php';
                    mysqli_select_db($conn, "dpi_project"); 
                    $result=mysqli_query($conn,"SELECT * FROM water_treatment_centre");   
                    $dataCount=mysqli_num_rows($result);   
                    for($i=0;$i<$dataCount;$i++){  
                        $result_arr=mysqli_fetch_assoc($result);  
                        $wtc_id=$result_arr['wtc_id'];  
                        $wtc_name=$result_arr['wtc_name'];  
                        $belong_catchment_id=$result_arr['belong_catchment_id']; 
                        $served_towns=$result_arr['served_towns'];
                        $served_population=$result_arr['served_population'];
                        $annual_water_use=$result_arr['annual_water_use'];  
                        $health_based_target_index=$result_arr['health_based_target_index']; 
                        $waste_water_quality_index=$result_arr['waste_water_quality_index'];
                        $water_supply_deficiency_index=$result_arr['water_supply_deficiency_index'];        
                        echo "<tr><td>$wtc_id</td><td>$wtc_name</td><td>$belong_catchment_id</td>"
                            . "<td>$served_towns</td><td>$served_population</td><td>$annual_water_use</td>"
                            . "<td>$health_based_target_index</td><td>$waste_water_quality_index</td><td>$water_supply_deficiency_index</td><tr>";
                        }
                ?>
                </tbody>
            </table>
        </div>
        <script>
            function delete_table(){
                var r=confirm("Are you sure to delete the current table?");
                if (r==true){
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            alert("Table Delete successfully.");
                            location.reload();
                        }
                    };
                    xhttp.open("POST", "tools/db_table_delete.php?table_name=water_treatment_centre", true);
                    xhttp.send();
                }
            }
            
            function import_data(files){
                var file = files[0];      
                form = new FormData();
                req = new XMLHttpRequest();
                form.append("file", file);
                req.onreadystatechange = function() {
                    if(req.readyState === 4 && req.status === 200) {
                        alert(this.responseText);
                        location.reload();
                    }
                };
                req.open("POST", 'tools/db_table_import.php?table_name=water_treatment_centre', true);
                req.send(form);
            }
            
             function download_table(){
                var req = new XMLHttpRequest();
                req.onreadystatechange = function() {
                    if(req.readyState === 4 && req.status === 200) {
                        if(this.responseText=="1"){
                            window.open("output.files/water_treatment_centre.csv");
                        }else{
                            alert("Fail to output the table.");
                        }
                    }
                };
                req.open("POST", 'tools/db_table_output.php?table_name=water_treatment_centre', true);
                req.send();
            }
        </script>            
    </body>
</html>

