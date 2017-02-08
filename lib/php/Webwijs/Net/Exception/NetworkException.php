<?php

namespace Webwijs\Net\Exception;

/**
 * Thrown to indicate that a network related operation has failed.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */
class NetworkException extends \Exception
{
    /**
     * Return a string representation of this exception.
     *
     * @return string a string representation of this exception.
     */
	public function __toString()
	{
		return sprintf('Network exception: %s (code: %s)', $this->getMessage(), $this->getCode());
	}
}
