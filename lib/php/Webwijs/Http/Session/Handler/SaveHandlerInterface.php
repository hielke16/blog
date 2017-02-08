<?php

namespace Webwijs\Http\Session\Handler;

/**
 * The SaveHandlerInterface is an interface that defines methods that are similar to 
 * the \SessionHandlerInterface interface available in the standard library of PHP 5.4.0. 
 * 
 * So for further documentation on how this interface can implemented see the official 
 * PHP documentation.
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 * @link http://php.net/manual/en/class.sessionhandlerinterface.php
 * @link http://php.net/manual/en/function.session-set-save-handler.php
 */
interface SaveHandlerInterface
{
    /**
     * Re-initialize existing session, or creates a new one.
     *
     * @param string $savePath the path where to store/retrieve the session.
     * @param string $name the session name.
     */
    public function open($savePath, $name);

    /**
     * Closes the current session.
     *
     * @return void
     */
    public function close();

    /**
     * Reads the session data from the session storage, and returns the results.
     *
     * @param string $id the session id.
     */
    public function read($id);

    /**
     * Writes the session data to the session storage.
     *
     * @param string $id the session id.
     * @param mixed $data The encoded session data.
     */
    public function write($id, $data);

    /**
     * Destroys a session.
     *
     * @param string $id the session ID being destroyed.
     */
    public function destroy($id);

    /**
     * Cleans up expired sessions.
     *
     * @param int $maxlifetime sessions that have not updated for the last maxlifetime 
     *                         seconds will be removed.
     */
    public function gc($maxlifetime);
}
