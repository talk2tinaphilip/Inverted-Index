<?php
namespace Team_apt\test_composer;
require_once "inverted_index.php";
require_once "document_map.php";
require_once "IndexFile.php";
if (sizeof($argv) < 3) {
    echo "Incorrect input";
    echo "\n";
    echo "Please Enter all files in this format :: \n php index_program.php path_to_folder_to_index index_filename\n ";
} else {	
	$indexFname=$argv[2];
	$size = 0;
	//main current directory
	$dirHome = getcwd();
    $inv    = new InvertedIndex();
	$indx   =   new IndexFile();
	global $dir ;
	$dir = $argv[1];
    $inv->begin($dir);	
	$file  = fopen( $dirHome . "/" . $indexFname.".txt", "wb" );
	$fname = $dirHome . "/" . $indexFname.".txt";
    ftruncate($file,0);
	$firstArray =  array_slice($postingListArray,0,1);
	$firstWord  =  key($firstArray);
	$keys       =  key($postingListArray);
	$lastWord   =  end((array_keys($postingListArray)));
	//function call to calculate dictionary offset
	$dictOffset    = $indx->calculateDictOffset($postingListArray,$lastWord,$file);
	fwrite($file,$dictOffset.",");	
	//function call to write dictionary words into index_file
	$indx-> writeDictWord($postingListArray,$file,$dictOffset,$firstWord);
	
	/*write gamma compressed offsets of posting lists and the corresponding word frequency to index_file
     */	
	foreach ($postingListArray as $t => $val) {
		$temp    = array();			
        $temp[0] = $val[0];
		$freq    = sizeof($val);
		$freqG   = $indx->gammaCode($freq);
		$sz      = sizeof($freqG);		
		if($freq>1){
		   //entering gamma coded frequency of word to index file
		    fwrite($file,",".$freqG);	
		}
		//finding delta-values of posting list 
	    for ($m = 0; $m < sizeof($val) - 1; $m++) {	
            $temp[$m+1] = $val[$m + 1] - $val[$m];             				
        }		
	    foreach ($temp as $k=>$delVal) {
			//find the gamma code and write its asciivalue to file
			$gamma     =$indx->gammaCode($delVal);
			$asciiValue=$indx->convertToAscii($gamma);
			fwrite($file,$asciiValue);
		} 

    }	
	
	/*write document map to index file which conatins entries such as 
	 *document id's followed by document lengths.
	 */	 
	$indexMaps= $indx->dDocumentMap($postingListPosArr,$file,$dirHome);
	fwrite($file,"\r\n");
	fclose($file);
	echo " \n Index File created .";
	
	
 }
 
 