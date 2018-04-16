<?php
namespace Team_apt\test_composer;

class MySimpleHeap extends \SplHeap
{
    protected $heap;
    
    public function __construct()
    {
        $this->heap = array();
    }
    
    public function isEmpty()
    {
        return empty( $this->heap );
    }
    // compare function to compare the score of two document that are added to heap
    public function compare( $item1, $item2 )
    {
        $a = key( $item1 );
        $b = key( $item2 );
        if ( $item1[$a] == $item2[$b] ) {
            return 0;
        }
        return ( $item1[$a] > $item2[$b] ? 1 : -1 );
    }
}