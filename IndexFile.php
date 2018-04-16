<?php
namespace Team_apt\test_composer;
require_once "inverted_index.php";
/**
 * Creation of Index File used to store entries of the form:
 * dictionary-stemmed words of corpus , posting list stored as  gamma-code compresses delta list offset 
 * followed by a gamma-coded frequency,and
 * document map with document id's followed by document lengths.
 * Dictionary-as-a-string approach is implemented for the creation of Index file.
 */
class IndexFile
{

	/**
	*Function to calculate gammacode of deltaList of each word .
	*/
	public function gammaCode($deltaNo)
	{
		$gamma ="";
		$bin = decbin ( $deltaNo );				
	    for($i=0; $i < strlen($bin)- 1; $i++){
		    $gamma = $gamma."0";
		}
		$gamma = $gamma.$bin;
		return $gamma;
	}
	
	
	/**
	*Function to calculate ascii value of binary/gamma-code .
	*/
	public function convertToAscii($bin)
	{    
	    $len=strlen($bin);	
		if ($len < 8){
		    $bin = str_pad($bin, 8, 0, STR_PAD_RIGHT);
			$ascii =chr( bindec ($bin));  	
	    }
		$ascii =chr( bindec ($bin));
		return $ascii;
	}
	
	/**
	*Function to calculate binary code of an ascii.
	*/
	public function revertFromAscii($ascii)
	{
		$bin = decbin(ord($ascii));
        $bin = str_pad($bin, 8, 0, STR_PAD_LEFT);
		return $bin;
	}
	
	/**
	*Function to calculate dictionary-offset
	*/
	public function calculateDictOffset($postingListArray,$lastWord,$file)
	{   $sizeFinal=0;
	    $size=0;
		$len=0;
		foreach ($postingListArray as $t => $val) {
		    if($t==$lastWord){
                $len+=strlen($t);			
		   	    $sizeFinal = strlen($t)+$size+strlen($len);
				//print_r("\nSZ:".$sizeFinal);
		    } else {
                    $len+=strlen($t);					
		            $size+=strlen($t)+strlen($len);
	            }				
	    }
	    return $sizeFinal;
	}
	
	
	public function writeDictWord($postingListArray,$file,$sizeFinal,$firstWord)
	{   
        foreach ($postingListArray as $t => $val) {
			if($t!=$firstWord){
				$len=$len+strlen($t);
				fwrite($file,$len.$t);
			}
			else {
				$len=0;
				$len=strlen($firstWord);
				fwrite($file,$len.$t);
			}		
		}		
	}
	
	
	/**
	*Function to create document map entries : document id followed by document length.
	*/	
	
	public function dDocumentMap($postingListArray,$file,$dir)
	{ 
	    $f="testres";
	    $f  = fopen( $dir . "/" . $f.".txt", "wb" );
		ftruncate($f,0);		
	    foreach ($postingListArray as $t => $val) {
	        global $indexMap;
			fwrite($f,$t.";");
		    foreach ($val as $docId => $pos) {	
                fwrite($f,$docId.",");
                fwrite($f,sizeof($pos).";");				
			    $temp    = array();
                $temp[0] = $pos[0]; 
                for ($m = 0; $m < sizeof($pos) - 1; $m++) {
                    $temp[] = $pos[$m + 1] - $pos[$m]; 
                }
                foreach ($temp as $position) {
				    $gm=self::gammaCode($position);					
                    if (array_key_exists($docId, $indexMap)) {
                        foreach ($indexMap[$docId] as $i) {
                            if ($t == $i) {
							    $indexMap[$docId][$i][] = $gm;                            		
                                return;
                            } 
                        }
                        $indexMap[$docId][$t][] = $gm;
                    }   else {
                        $indexMap[$docId]       = array();
                        $indexMap[$docId][$t][] = $gm;			
                        }		
                }
            } fwrite($f,".");		
	    }   	   
		ksort($indexMap);
		$len=NULL;
	    $docMapOffset = sizeof($indexMap);
		fwrite($file,",".$docMapOffset.",");			
		foreach($indexMap as $docid => $val )  {
	        $mapLength=0;
			fwrite($file,$docid."-");
			$mapLength=sizeof($val);    //mapLength is the no:of distinct stem words in a document.
			fwrite($file,$mapLength." ");
	    } 
	    return $this->$indexMap;
	}
	
	/**
	*Function to read dictionary words
	*/
	
	public function readDictWord($dictArr)
	{  $i=0;
	   foreach($dictArr as $val){
			if(!empty($val))
			{
				$val=substr($val,1,strlen(trim($val)));
				$dictArray[$i++]= $val; 	
		 	}
		
		}   return $dictArray;
	  
		  
	}
	
	public function checkQuery($dictArray,$queryIndex,$dirHome)
	{  
	    foreach($queryIndex as $q){
			foreach($dictArray as $d){
				if($q==$d){
					$flag=1;
				} else{
					$flag=0;
				}
			}
		}
		return $flag;
	}
	
	public function unitT($dir)
	{   $i=0;
	    $fp=fopen($dir . "/" ."testres.txt","r");
		while(!feof($fp)){
	        $line=fgets($fp);
		}
		$exp=explode(".",$line);
		foreach ($exp as $val){	
		    $list = explode(";",$val);
			for($m=1;$m < sizeof($list)- 1 ; $m ++){
			    $temp = $list[$m];
				$n = $m-1;					
				$posArray[$list[0]][substr($temp,0,2)][]= substr($temp,3);					
			}
		}
		 fclose($fp);
		 return $posArray;
	}
}
