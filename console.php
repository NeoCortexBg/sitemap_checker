<?php

require_once __DIR__ . '/color.php';

class Console
{
	private static $instance;

	protected static $ansiColorMap = array(
		'fg' => array(
			Color::NORMAL => '22;39',
			Color::RESET => '22;39',

			Color::BLACK => '0;30',
			Color::RED => '0;31',
			Color::GREEN => '0;32',
			Color::YELLOW => '0;33',
			Color::BLUE => '0;34',
			Color::MAGENTA => '0;35',
			Color::CYAN => '0;36',
			Color::WHITE => '0;37',

			Color::GRAY => '1;30',
			Color::LIGHT_RED => '1;31',
			Color::LIGHT_GREEN => '1;32',
			Color::LIGHT_YELLOW => '1;33',
			Color::LIGHT_BLUE => '1;34',
			Color::LIGHT_MAGENTA => '1;35',
			Color::LIGHT_CYAN => '1;36',
			Color::LIGHT_WHITE => '1;37',
		),
		'bg' => array(
			Color::NORMAL => '0;49',
			Color::RESET => '0;49',

			Color::BLACK => '40',
			Color::RED => '41',
			Color::GREEN => '42',
			Color::YELLOW => '43',
			Color::BLUE => '44',
			Color::MAGENTA => '45',
			Color::CYAN => '46',
			Color::WHITE => '47',

			Color::GRAY => '40',
			Color::LIGHT_RED => '41',
			Color::LIGHT_GREEN => '42',
			Color::LIGHT_YELLOW => '43',
			Color::LIGHT_BLUE => '44',
			Color::LIGHT_MAGENTA => '45',
			Color::LIGHT_CYAN => '46',
			Color::LIGHT_WHITE => '47',
		),
	);

	/**
	 *
	 * @return Console
	 */
	public static function getInstance()
	{
		if(!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function getColorCode($color, $type = 'fg')
	{
		if(!isset(self::$ansiColorMap[$type][$color])) {
			throw new \Exception('Color '.$color.' does not exist');
		}
		return self::$ansiColorMap[$type][$color];
	}

	public function getColoredText($text, $color = null, $bgColor = null)
	{
		return
			(($color === null) ? "" : "\033[".$this->getColorCode($color, 'fg')."m")
			.(($bgColor === null) ? "" : "\033[".$this->getColorCode($bgColor, 'bg')."m")
			.$text
			."\033[0m";
	}

	public function write($text, $color = null, $bgColor = null)
	{
		echo $this->getColoredText($text, $color, $bgColor);
	}

	public function writeLine($text, $color = null, $bgColor = null)
	{
		echo $this->write($text, $color, $bgColor).PHP_EOL;
	}
}
