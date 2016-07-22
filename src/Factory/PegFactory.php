<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 1:39 PM
 * 
 */

namespace AlphaPipe\MasterMind\Factory;

use AlphaPipe\MasterMind\Peg;

class PegFactory
{
	public static function makePeg($color, $position = 0)
	{
		return new Peg($color, false, $position);
	}

	public static function makeControlPeg($color)
	{
		return new Peg($color, true);
	}
}

