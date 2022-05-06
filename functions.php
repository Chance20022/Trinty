<?php 
    function convertDataFromBD($data){
        $masData = [""];
        $counter = 0;
        for($i = 0; $i < strlen($data); $i++){
            if($data[$i] != ",") $masData[$counter] .= $data[$i];
            else $counter++;
        }
        return $masData;
    }

    function splitIntoKeys($data){
        
    }
?>