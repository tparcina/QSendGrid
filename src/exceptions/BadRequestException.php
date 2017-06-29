<?php

namespace QAlliance\Exceptions;

use \Exception;

/**
 * BadRequestException
 */
class BadRequestException extends Exception
{
	const STATUS_CODE = 400;

	public function __construct($message)
	{
		parent::__construct($message, self::STATUS_CODE);
	}

}