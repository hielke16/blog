<?php

namespace Webwijs\View\Helper;

class RedirectWithMessage
{
    public function redirectWithMessage($message, $type = 'success', $url = null)
    {
        if (is_null($url)) {
            $url = $_SERVER['REQUEST_URI'];
        }
        $_SESSION['webwijs']['messages'][] = array('type' => $type, 'message' => $message);
        wp_redirect($url);
        exit;
    }
}
