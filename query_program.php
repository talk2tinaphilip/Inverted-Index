<?php
namespace Team_apt\test_composer;
require_once "inverted_index.php";
require_once "IndexFile.php";
require_once "bm25_ranking.php";
if (sizeof($argv) < 4) {
    echo "Incorrect input";
    echo "\n";
    echo "Please Enter all files in this format : \n php query_program.php index_filename query relevance_measure\n ";
}   else {
	    $fname        = $argv[1];
	    $dirHome      = getcwd();
	    $fn           = $dirHome . "/".$fname.".txt";
	    $file         = fopen($dirHome . "/".$fname.".txt", "r+" );
	    $query        = $argv[2];
        $querytolower = strtolower( $query );
        $queryArr     = explode( " ", $querytolower );
		$inObj        = new IndexFile();		  
	    $queryIndex   = InvertedIndex::setTokenization("stem", $queryArr );
		$pos          = $inObj->unitT($dirHome);
	    $wordsArr       =array();
	    $i=0;$j=0;
	    $count=0;
	    $lc=0;
        $lt=0; 
        $ch=NULL;
		 
		//Get Dictionary offset
        
        while(!feof($file)) {
		    $line=fgets($file);
			if ($line =="\r\n"){	
               break;
		    }
            $dict=explode(",",$line);	   		
            //Read Dictionary stemmed words			
		    $dictWords= substr($line,strlen($dict[0]),$dict);
			$dictArray= $inObj->readDictWord($dictArr);			
			$flag     =	$inObj->checkQuery($dictArray,$queryIndex,$dirHome);			
        }	
	   foreach ($pos as $t => $val) {
	        global $indexMap;
			foreach ($val as $docId => $f) {	                			
			    $temp    = array();
                $temp[0] = $f[0];                   
                foreach ($temp as $position) {					
                    if (array_key_exists($docId, $indexMap)) {
                        foreach ($indexMap[$docId] as $i) {
                            if ($t == $i) {
							    $indexMap[$docId][$i][] = $position;                            		
                                return;
                            } 
                        }
                        $indexMap[$docId][$t][] = $position;
                    }   else {
                        $indexMap[$docId]       = array();
                        $indexMap[$docId][$t][] = $position;			
                        }		
                }
            } 
		   
        }  
		
		foreach($queryIndex as $q) {
			 foreach ($indexMap as $doc => $val ){
				foreach ($val as $t => $freq){
					$i=0;
					if($q==$t){
                        $key=$doc;
					    $ld[$doc]=sizeof($val);
					    $numberOfFiles=sizeof($ld);
				    }				
			    } 		
			}
		 }  
			
		foreach ($queryIndex as $q){
			foreach ($pos as $term => $arr) {
				$lc = sizeof($pos);               // lc is the total no:of tokens in the collection
				if(array_key_exists ($q,$pos)){
				    foreach ($arr as $d=> $arr2 ){
					    foreach ($arr2 as $val){
							$lt = $lt+$val ;       // lt is the no:of times term t occurs in the collecion.
						    
					    }
					 
				    }
				}	
			}
		}
		
			  
		if ( strcasecmp( $argv[3], "bm25" ) == 0 ) {
		    
            BM25Ranking::getBM25SCORE($indexMap,$pos, $queryIndex, $numberOfFiles,$ld,$lt,$lc);
		   
		  
	    }     
		  
		  
	  
}	  
		  
		 	   

          
	


