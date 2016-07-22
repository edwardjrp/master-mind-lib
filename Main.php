<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/2/16
 * Time: 12:18 AM
 * 
 */

require_once __DIR__.'/vendor/autoload.php';

use AlphaPipe\MasterMind\Common\PegColor;
use AlphaPipe\MasterMind\Peg;
use AlphaPipe\MasterMind\PegBoard;


$board = new PegBoard();
$guess = [
	new Peg( PegColor::RED ),
	new Peg( PegColor::BLUE ),
	new Peg( PegColor::GREEN),
	new Peg( PegColor::YELLOW ),
	new Peg( PegColor::WHITE )
];

$result = $board->guess($guess);
dump($result);

//Dumping the secret code to do a visual comparision of the result
dump($board->getSecretCode());