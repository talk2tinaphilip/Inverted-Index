<?php
namespace Team_apt\test_composer;

require_once "inverted_index.php";
require_once "heap_file.php";
/* This class will calculate the BM25Ranking of Documents based on Query terms*/
class BM25Ranking
{
    //Computes BM25 score for Query terms
    public static function getBM25SCORE( $fileArray,$pList, $queryIndex, $numberOfFiles,$lengthofDoc,$lt,$lc)
    {  
        $zero         = 0;
        $count        = 0;
        $bm25heap     = new MySimpleHeap(); //Max heap for the bm25 score
        $queryminheap = new \SplMinHeap; // Min heap for the query Index
        for ( $i = 0; $i < sizeof( $queryIndex ); $i++ ) {
            $queryminheap->insert( $queryIndex[$i] ); //inserting query index into queryminheap
        }
        foreach ( $queryminheap as $qt ) {
            $querylist[] = $qt;		
        } 		
        $tfIdfDocArray = self::tf_IdfCalculatorDoc( $pList, $querylist, $numberOfFiles, $lengthofDoc ,$lt,$lc);
		
        for ( $a = 0; $a < $numberOfFiles; $a++ ) {
            $score = 0;
            foreach ( $querylist as $qt ) {
                $score += $tfIdfDocArray[$a][$qt];
				
            }
            $bm25heap->insert( array( $a => $score ) ); //Adding the non zero scores to the bm25heap 
     
        }  
        // Displaying results in trec_eval format query_id iter docno rank sim run_id
		
		ksort($fileArray);
		print_R($fileArray);
		
        while ( $count < 100 ) {
            $value = $bm25heap->extract();
            foreach ( $value as $key => $val ) {
                foreach ( $fileArray as $docid => $fname ) {
                    if ( $key == $docid ) {
                        if($fname < 10 ){
							$rank =$count+1;
						    echo " 0 0$docid  $rank " . $val . " bm25 \n";
                            $count++;	
						} else {
							$rank =$count+1;
							echo " 0 $docid $rank " . $val . " bm25 \n";
                            $count++;
						}
                        
                    }
                }
            }
        }
		
    }
    //Computes TF-IDF for query terms as they occur in document
    public static function tf_IdfCalculatorDoc( $pList, $querylist, $numberOfFiles, $lengthofDoc,$lt,$lc )
    {    
        $tfQuery  = self::tfBm25( $pList,$querylist, $numberOfFiles, $lengthofDoc,$lt,$lc );
        $idfQuery = self::idfCalculator( $pList, $querylist, $numberOfFiles, $lengthofDoc, $lt, $lc);
        for ( $i = 0; $i < $numberOfFiles; $i++ ) {
            foreach ( $querylist as $qt ) {
                $tf_idfDoc[$i][$qt] = $tfQuery[$i][$qt] * $idfQuery[$qt];
            }
        }
        return $tf_idfDoc;
    }
    //Computes the Term Frequency for query terms as they occur in document
    public static function tfBm25( $pList, $queryIndex, $numberOfFiles, $lengthofDoc,$lt,$lc )
    {   //print_r($numberOfFiles);
	    $k1       = 1.2;
        $b        = 0.75;
		$cmp=        10;
        $fieldAvg = array_sum( $lengthofDoc ) / $numberOfFiles;
        $freq;
        $totalterms = array();
        $l          = array();
        for ( $i = 0; $i < $numberOfFiles; $i++ ) {
            foreach ( $pList as $term => $dict ) {
                $totalterms[] = $term;
                foreach ( $dict as $k => $v1 ) {
					foreach ($v1 as $ind => $freq){
						
						if($k < $cmp )
						{
							$k=substr($k,1,2);
							$l[$k][$term] = $freq;
						}
						else {
						    $l[$k][$term] = $freq;
						}
					}
                    
                }
            } 
			foreach ( $queryIndex as $qt ) {
                if ( !array_key_exists( $qt, $l[$i] ) ) {
                    $freq             = 0;
                    $tfOfDoc[$i][$qt] = 1;
                } else {
                    $freq             = $l[$i][$qt];
                    $tfOfDoc[$i][$qt] = ( $freq * ( $k1 + 1 ) ) / ( $freq + $k1 * ( ( 1 - $b ) + ( $b * ( $fieldLength[$i] / $fieldAvg ) ) ) );
                }
            }
        }
        return $tfOfDoc;
	}
			
	
    //Computes IDF for query terms as they occur in document    
    public static function idfCalculator( $pList, $queryIndex, $numberOfFiles,$ld, $lt, $lc )
    {   
	    $freq2 = 0;
		foreach ($ld as $doc ){
		    $lmdDr=$doc + 1000;
            $l     = array();
            foreach ( $pList as $term => $dict ) {
                $totalterms[] = $term;
                foreach ( $dict as $k1 => $v1 ) {
                    $l[$k1][$term] = sizeof( $v1 );
                }
            }
           foreach ( $queryIndex as $qt ) {
                if ( array_key_exists( $qt, $pList ) ) {
                   $freq2 += 1;
                } else {
                    $freq2 = 0;
                }
				$lmdNr = $freq2 + $dm ;
                if ( $freq2 > 0 ) {
                    $idfOfQuery[$qt] = log( ( 1 + ( $numberOfFiles / sizeof( $pList[$qt] ) ) ), 2 );
                } else {
                    $idfOfQuery[$qt] = $lmdNr /$lmdDr;  // LMD Smoothing
                }
            }
		}
        return $idfOfQuery;
      
    }
}