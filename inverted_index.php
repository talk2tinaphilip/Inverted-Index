<?php
namespace Team_apt\test_composer;

use seekquarry\yioop\configs as SYC;
use seekquarry\yioop\library as SYL;
use seekquarry\yioop\library\PhraseParser;
require_once "vendor/autoload.php";
//This class is used to create a Inverted Index of the terms in Document Corpus
class InvertedIndex
{
    function begin($dir)
    {
        if (!isset($dir)) {
            echo "Enter directory path!\r\n";
            return;
        }
        $directory = $dir;
        $fileArray = glob($directory . "/" . '*.txt');
		global $lengthOfDoc;
        global $numberOfFiles;
        $numberOfFiles = sizeof($fileArray);
        foreach ($fileArray as $key => $value) {
			$documentId[$value] = $key;
			//$id                 = $documentId[$value];
			$id=trim(basename("$value",".txt").PHP_EOL);
			$myFileName         = $value;
			$fh                 = fopen($myFileName, 'r');
            while (!feof($fh)) {
                $theData     = fgets($fh);
                $s           = preg_replace("/[^a-zA-Z 0-9]+/", " ", $theData);
                $toLowerCase = strtolower($s);
                $_term       = explode(" ", $toLowerCase);
				$lengthOfDoc[]   = sizeof( $_term );
                // Tokenizing the words present in files
                $term = $this->setTokenization("Stem", $_term);			
                for ($i = 1; $i <= sizeof($term); $i++) {
                    $temp = $term[$i-1];
					if ($temp != "") {
                        $this->countWords($temp, $myFileName, $i, $id);
                    }
                }
            }
            fclose($fh);
        }
    }
    function countWords($temp, $myFileName, $posIndex, $docId)
    {
        global $searchArray;
        if (array_key_exists($temp, $searchArray)) {
            $searchArray[$temp][0]++;
            $x                     = $searchArray[$temp][0];
            $test                  = $this->docCount($myFileName, $temp);
            $searchArray[$temp][1] = $test;
        } else {
            $searchArray[$temp]    = array();
            $searchArray[$temp][0] = 1;
            $x                     = $searchArray[$temp][0];
            $test                  = $this->docCount($myFileName, $temp);
            $searchArray[$temp][1] = $test;
        }
        $this->postingList($posIndex, $temp, $docId);
		$this->postingListPos($posIndex, $temp, $docId);
    }
    function docCount($myFileName, $temp)
    {
        global $docCountArray;
        if (array_key_exists($temp, $docCountArray)) {
            // array already initialised
        } else {
            $docCountArray[$temp] = array();
        }
        if (in_array($myFileName, $docCountArray[$temp])) {
            return count($docCountArray[$temp]);
        } else {
            array_push($docCountArray[$temp], $myFileName);
            return count($docCountArray[$temp]);
        }
    }
    function postingList($posIndex, $temp, $docId)
    {
        global $postingListArray;		
		if (array_key_exists($temp, $postingListArray)) {
			$c=0;
            foreach ($postingListArray[$temp] as $c => $i) {
                if ($docId == $i) {
                    return;
                }
				$c++;
            }
            $postingListArray[$temp][] = $docId;
        } else {
            $postingListArray[$temp]           = array();
            $postingListArray[$temp][] = $docId;
            
        }
        ksort($postingListArray);
    }
	
	function postingListPos( $posIndex, $temp, $docId )
    {
        global $postingListPosArr;
        if ( array_key_exists( $temp, $postingListPosArr ) ) {
            foreach ( $postingListPosArr[$temp] as $i ) {
                if ( $docId == $i ) {
                    $postingListPosArr[$temp][$i][] = $posIndex;
                    return;
                }
            }
            $postingListPosArr[$temp][$docId][] = $posIndex;
        } else {
            $postingListPosArr[$temp]           = array();
            $postingListPosArr[$temp][$docId][] = $posIndex;
            
        }
        ksort( $postingListPosArr );
    }
	
    public static function setTokenization($arguments, $terms)
    {
        $tokenList = $terms;
        $tz        = "stem";
        $cz        = "chargram";
        $n         = 5;
        if (strcasecmp($arguments, $tz) == 0) {
            $wordlist = PhraseParser::stemTerms($tokenList, 'en-US');
            return $wordlist;
        } elseif (strcasecmp($arguments, $cz) == 0) {
            $wordlist = PhraseParser::getNGramsTerm($tokenList, $n);
            return $wordlist;
        } elseif (strcasecmp($arguments, "none") == 0) {
            $wordlist = $terms;
            return $wordlist;
        }
    }
	
	public function test()
	{
		print_R($postingListPosArr);
	}
	
	
}
$searchArray      = array();
$docCountArray    = array();
$postingListArray = array();
$postingListPosArr = array();
$lengthOfDoc      = array();
$numberOfFiles;