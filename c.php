<?php
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, HEAD');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Max-Age: 1728000');
    header('Access-Control-Allow-Origin: *'); 

    header('Content-type:application/json;charset=utf-8');
    include "config.php";
    
    if (mysqli_connect_errno()) { 
        printf("Connect failed: %s\n", mysqli_connect_error()); 
        exit(); 
    } 
    
    $json = file_get_contents('php://input');
    $data = json_decode($json,true);
    $dsp = $data['sp'];
    $dspp = $dsp."(";
    $adsp = array();
    $x = array();
    $typee = array();
    foreach ($data as $j => $val) {
        if($j == "sp"){
            continue;
        }else {
            $dt = "?";
            $a = $val;
            array_push($adsp, $dt);
            array_push($x, $a);
        }
    }
    $idsp = implode(",",$adsp);
    $dsppp = $dspp.$idsp.")";
    $query = "CALL $dsppp";
        
    for($i=0;$i<count($x);$i++){
        $t = gettype($x[$i])=="string" ? "s":"i";
        array_push($typee, $t);
    }
    $imp = implode('',$typee);
    $args = array(&$imp);
    
    for ($i=0;$i<count($x);$i++){
            $args[] = &$x[$i];
    }

    if ($stmt = $con->prepare($query)) { 
        if($idsp != ''){
            call_user_func_array(array($stmt, 'bind_param'), $args);
        }else{
            echo "";
        }
                 
        $stmt->execute(); 
        if(strpos($dsp, 'search')==true || strpos($dsp, 'select')==true){
            $meta = $stmt->result_metadata(); 

            while ($field = $meta->fetch_field()) 
            { 
                $params[] = &$row[$field->name]; 
            } 

            call_user_func_array(array($stmt, 'bind_result'), $params); 

            while ($stmt->fetch()) { 
                foreach($row as $key => $val) 
                { 
                    $c[$key] = $val; 
                } 
                $result[] = $c; 
            } 
            $a = json_encode($result);
            printf($a);
        }else{
            if($stmt->affected_rows==1){
                echo '{"success":"success"}';   
            }else{
                echo $stmt->error;
            }
        }
        
        $stmt->close(); 
            }
     
    $con->close(); 

    ?>