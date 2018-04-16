
#Owner: Tina Philip
#Aim: Create Inverted Index of a given collection of text documents and retrieve the documents in order of relevance, upon giving a conjunctive query.

#Steps:
1. index_program.php
Index a given folder filled with plain text docs.
2. query_program.php
Query the index file created with relevance_measure of BM25 and a choice of DFR/LMJM/LMD.

#Functionality:    
	index_program.php       : creates index file
	query_program.php	: queries the terms and gives relevance of documents
	IndexFile.php
	inverted_index.php      : contains class InvertedIndex which creates Inverted Index of the terms in document corpus and handles tokenization using YIOOP and composer.
	BM25Ranking.php	        : contains class BM25Ranking that handles the Ranking of Documents given bm25 as ranking method	
	heapfile.php	        : contains the heap which uses compare() to compare the score of two document that are added to heap
	Composer.json
	composer.lock
    	subfolder            	: corpus contains text files

#How to Run from Command Line:
1. index_program.php
php index_program.php path_to_folder_to_index index_filename
2. query_program.php
php query_program.php index_filename query relevance_measure


#Expected Output:
1. index_program.php
The program on input such as the above should then write an index to index_filename. For this index, the dictionary should only store stemmed versions of words, and should take a dictionary-as-string approach to its layout. Posting lists should be stored as gamma-code compressed delta lists of offsets into a document map. A document map entry should consist of document id, a map entry length, document length, sorted list of distinct stemmed terms in the document and their frequencies.

2. query_program.php
The program on the above input should use the index and compute a conjunctive query of the terms in query and score the resulting documents using the provided relevance measure.