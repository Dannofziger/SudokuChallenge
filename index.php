<?php
/* Dev Notes (mostly for me)
 * As much as I want this to be scalable, sudoku doesn't vary size.
 * 
 * Options:
 * 1. Object for each box referenced by it's own X and Y values...
 *   but I don't want to make 81 objects.
 * 2. Array of array: time complexity sucks, but doesn't need to scale...
 *   it's possible, even probably the best option.
 * 3. Single array of strings and automatically keep track of 9 count...
 *   could get hairy and confusing with dealing with 3x3 square.
 * 
 * Array of array it is.
 * 
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 * [0][0][0][0][0][0][0][0][0]
 */

class Box {
    //makin' a box
    private $colStart;
    private $colEnd;
    private $rowStart;
    private $rowEnd;
    //Apparently __contruct is the way to make it...
    public function __construct($boxNum) {
        //Switch statement? switch statement.
        switch ($boxNum) {
            case 0:
                $this->colStart = 0;
                $this->colEnd = 2;
                $this->rowStart = 0;
                $this->rowEnd = 2;
                break;
            case 1:
                $this->colStart = 3;
                $this->colEnd = 5;
                $this->rowStart = 0;
                $this->rowEnd = 2;
                break;
            case 2:
                $this->colStart = 6;
                $this->colEnd = 8;
                $this->rowStart = 0;
                $this->rowEnd = 2;
                break;
            case 3:
                $this->colStart = 0;
                $this->colEnd = 2;
                $this->rowStart = 3;
                $this->rowEnd = 5;
                break;
            case 4:
                $this->colStart = 3;
                $this->colEnd = 5;
                $this->rowStart = 3;
                $this->rowEnd = 5;
                break;
            case 5:
                $this->colStart = 6;
                $this->colEnd = 8;
                $this->rowStart = 3;
                $this->rowEnd = 5;
                break;
            case 6:
                $this->colStart = 0;
                $this->colEnd = 2;
                $this->rowStart = 6;
                $this->rowEnd = 8;
                break;
            case 7:
                $this->colStart = 3;
                $this->colEnd = 5;
                $this->rowStart = 6;
                $this->rowEnd = 8;
                break;
            case 8:
                $this->colStart = 6;
                $this->colEnd = 8;
                $this->rowStart = 6;
                $this->rowEnd = 8;
                break;
            default:
                echo "You flubbed up";
        }
    }
    public function getColStart(){
        return $this->colStart;
    }
    public function getRowStart(){
        return $this->rowStart;
    }
    public function getColEnd(){
        return $this->colEnd;
    }
    public function getRowEnd(){
        return $this->rowEnd;
    }
}

class Sudoku {
    //Set up us the bomb
    private $_matrix;
    //Ok, '_' seems to be a holdover from PHP days where private wasn't
    //  something you could set. Is it still best practice to use _?

    //Handle input
    public function handleInput(array $input = null) {
        // In some of the examples I saw, it appeared that I had to be explicit
        //   in my arguement, and in some simply $input seemed to suffice.
        if (!isset($input)) {
            $this->_matrix = $this->emptyPuzzle();
        } else {
            $this->_matrix = $input;
        }
    }
    //I need to create a new puzzle for no input
    public function emptyPuzzle(){
        //return an empty array of arrays 9x9
        return array_fill(0, 9, array_fill(0, 9, 0));
        //AHHHHH! You can start an array at a nonzero number with array_fill >:C
        //  no one being should have that of power.
    }
    //Next we have to figure out what's possible in a row/col/box
    public function rowPossible($number, $row) {
        $possible = true;
        $i = 0;
        while ($i < 9){
            if($this->_matrix[$row][$i] == $number){
                $possible = false;
                break;
            }
            $i++;
        }
        return $possible;
    }
    public function colPossible($number, $col) {
        $possible = true;
        $i = 0;
        while ($i<9) {
            if($this->_matrix[$i][$col] == $number){
                $possible = false;
            }
            $i++;
        }
        return $possible;
    }
    public function boxPossible($number, $boxNum) {
        $possible = true;
        $NuBox = new Box($boxNum);
        $i = $NuBox->getRowStart();
        while($i <= $NuBox->getRowEnd()){
            $j = $NuBox->getColStart();
            while($j <= $NuBox->getColEnd()){
                if($this->_matrix[$i][$j] == $number){
                    $possible = false;
                }
                $j++;
            }
            $i++;
        }
        return $possible;
    }
    //With our powers combined... we can determine if this works.
    //Which brings us a bug. I'd like to be able to do this without row/col/box
    public function numPossible($number, $row, $col, $box) {
        return $this->rowPossible($number, $row) and $this->colPossible($number, $col) and $this->boxPossible($number, $box);
    }
    // function kimPossible() {}
    //Gotta check if it's all done.
    public function isSolvedSudoku(){
        $isSolved = true;
        for ($i = 0; $i < 9; $i++){
            if (!$this->isSolvedRow($i) || !$this->isSolvedCol($i) || !$this->isSolvedBox($i)) {
                $isSolved = false;
            }
        }
        return $isSolved;
    }
    //is the row solved?
    public function isSolvedRow($row){
        $arrayCheck = $this->_matrix[$row];
        if(array_diff(array(1,2,3,4,5,6,7,8,9), $arrayCheck)!= 0){
            return false;
        }else{
            return true;
        }
    }
    //is the column solved?
    public function isSolvedCol($col){
        $arrayCheck = array();
        for ($i = 0; $i < 9; $i++){
            $arrayCheck[$i] = $this->_matrix[$i][$col];
        }
        if(array_diff(array(1,2,3,4,5,6,7,8,9), $arrayCheck)!= 0){
            return false;
        }else{
            return true;
        }
    }
    //is the box solved?
    public function isSolvedBox($box){
        $arrayCheck = array();
        $NuBox = new Box($box);
        $i = $NuBox->getRowStart();
        while ($i <= $NuBox->getRowEnd()) {
            $j = $NuBox->getColStart();
            while ($j <= $NuBox->getColEnd()) {
                array_push($arrayCheck, $this->_matrix[$i][$j]);
                $j++;
            }
            $i++;
        }
        if(array_diff(array(1,2,3,4,5,6,7,8,9), $arrayCheck)!= 0){
            return false;
        }else{
            return true;
        }
    }
    //educated guessing
    public function possibleValues ($cell){
        $possible = array();
        for($i = 1; $i <= 9; $i++){
            if(!$this->numPossible($cell, $i)){
                array_unshift($possible, $i);
            }
        }
        return $possible;
    }
    //uneducated guessing
    public function randomPossibleValues ($possible, $cell){
        return $possible[$cell][rand(0, count($possible[$cell])-1)];
    }
    //no guessing
    public function findUnique () {
        for($i = 0; $i <9; $i++){
            for($j = 0; $j < 9; $j++){
                $matrixValue = $this->_matrix[$i][$j];
                if($matrixValue == 0){
                    $cell = $i*9+$j;
                    $possible[$cell] = $this->possibleValues($cell);
                    if(count($possible[$cell]) == 0){
                        return(false);
                    }
                }
            }
        }
    }
    //Step back if we run into a dead end
    public function goBack ($attempt_array, $number){
        $nuArray = array();
        for($x = 0; $x < count($attempt_array); $x++){
            if($attempt_array[$x] != $number){
                array_unshift($nuArray, $attempt_array[$x]);
            }
        }
        return $nuArray;
    }

    public function nextRandom ($possible){
        $max = 9;
        //$possible is 80 cells long
        for($i = 0; $i <= 80; $i++){
            if((count($possible[$i])<=$max) and (count($possible[$i])>0)){
                $max = count($possible[$i]);
                $min = $i;
            }
        }
        return $min;
    }
    //Print sudoku
    function exportSudoku(){
        $html = '<table bgcolor = \"#000000\" cellspacing = \"1\" cellpadding = \"2\">';
        for ($i = 0; $i < 9; $i++){
            $html .= '<tr bgcolor = \"white\" align = \"center\">';
            for($j = 0; $j < 9; $j++){
                $html.= '<td width = \"20\" height = \"20\">".$this->_matrix[$i][$j]."</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
    //solve the thing
    public function solve(){
		$i=0;
        $saved = array();
        $savedSudoku = array();
        $start = microtime();
        while(!$this->isSolvedSudoku()){
                $i+=1;
                $nextMove = $this->findUnique();
                if($nextMove == false){
                        $nextMove = array_pop($saved);
                        $sudoku = array_pop($savedSudoku);
                }
                $nextTry = $this->nextRandom($nextMove);	
                $attempt = $this->randomPossibleValues($nextMove,$nextTry);
                if(count($nextMove[$nextTry])>1){					
                        $nextMove[$nextTry] = $this->goBack($nextMove[$nextTry],$attempt);
                        array_push($saved,$nextMove);
                        array_push($savedSudoku,$sudoku);
                }
                $sudoku[$nextTry] = $attempt;	
        }
        $end = microtime();
        $ms_start = explode(" ",$start);
        $ms_end = explode(" ",$end);
        $total_time = round(($ms_end[1] - $ms_start[1] + $ms_end[0] - $ms_start[0]),2);
        echo "completed in $x steps in $total_time seconds";
        echo print_sudoku($sudoku);
        }   
    }
$S = new Sudoku();
$S->solve();
