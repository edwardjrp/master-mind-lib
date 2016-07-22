<?php
/**
 * Created by Edward Rodriguez
 * Date: 7/9/16
 * Time: 1:30 PM
 * 
 */

namespace AlphaPipe\MasterMind;

use AlphaPipe\MasterMind\Common\PegColor;
use AlphaPipe\MasterMind\Common\PegTrait;
use AlphaPipe\MasterMind\Contracts\PegBoardInterface;
use AlphaPipe\MasterMind\Exceptions\InvalidGuessInput;
use AlphaPipe\MasterMind\Exceptions\InvalidGuessPeg;
use AlphaPipe\MasterMind\Factory\PegFactory;
use Illuminate\Support\Collection;

class PegBoard implements PegBoardInterface
{

	use PegTrait;

	/**
	 * @var Collection
	 */
	private $secretCode;

	private $attempts = 0;

	private $guessResponse = [];

	private $guessPlay;

	public function __construct()
	{
		$this->secretCode = new Collection();
		$this->createSecret();
		$this->guessResponse = new Collection();
		$this->guessPlay = new Collection();
	}

	public function guess( array $guess )
	{
		$play = $this->validatePlay($guess);
		if (!$play->isValidPlay()) {
			throw new InvalidGuessInput;
		}

		if ($this->canPlay()) {
			$this->compareGuess();
		}

		return [
			'attemptsLeft'  => $this->attemptsLeft(),
			'guessResponse' => $this->getGuessResponse(),
			'guessPlayed'   => $this->guessPlay
		];
	}

	private function compareGuess()
	{
		$this->guessPlay->each(function($peg) {
			//Check peg are same color and same position
			if ($this->getSecretCode()->contains($peg)) {
				return $this->guessResponse->push(PegFactory::makeControlPeg(PegColor::RED));
			}

			//Check which pegs match color and have different position
			$this->getSecretCode()->each(function($s) use ($peg) {
				if (($peg->getColor() == $s->getColor()) && ( $peg->getPosition() != $s->getPosition() )) {
					return $this->guessResponse->push(PegFactory::makeControlPeg(PegColor::WHITE));
				}
			});

		});

		$this->computeGuess();
	}

	private function validatePlay(array $guess) {
		$position = 1;
		foreach($guess as $g) {
			if (!($g instanceof Peg)) {
				throw new InvalidGuessPeg;
			}
			if($g->isControlPeg()) {
				throw new InvalidGuessPeg('Control pegs are for response purposes only');
			}

			/**
			 * If no position is specify when the peg object was created, it will auto-assign positions in
			 * the order the peg comes in the array
			 */
			if ($g->getPosition() == 0) {
				$g->setPosition($position);
			}

			$this->guessPlay->push($g);
			$position++;
		}
		return $this;
	}

	public function isValidPlay()
	{
		return ($this->guessPlay->count() == $this->guessLimit());
	}

	/**
	 * @return Collection
	 */
	public function getSecretCode()
	{
		return $this->secretCode;
	}

	public function canPlay()
	{
		return ($this->getAttempts() <= $this->playLimit());
	}

	public function attemptsLeft()
	{
		return ($this->playLimit() - $this->getAttempts());
	}

	/**
	 * @return int
	 */
	public function getAttempts()
	{
		return $this->attempts;
	}

	private function computeGuess()
	{
		$this->attempts++;
	}


	private function createSecret()
	{
		if ($this->getSecretCode()->isEmpty()) {
			$this->addPegs();
		}
	}

	private function addPegs()
	{
		for($i = 0; $i <= 4; $i++ ) {
			$this->secretCode->push(PegFactory::makePeg($this->pickRandomColor(), ($i + 1)));
		}
	}

	/**
	 * @return Collection
	 */
	public function getGuessResponse()
	{
		return $this->guessResponse;
	}

}

