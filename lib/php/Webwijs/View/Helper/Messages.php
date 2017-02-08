<?php

namespace Webwijs\View\Helper;

class Messages
{
    public function messages()
    {
        $output = '';
        if (!empty($_SESSION['webwijs']['messages'])) {
            foreach ((array) $_SESSION['webwijs']['messages'] as $message) {
                $output .= '<div id="message" class="' . $message['type'] . '"><p>' . __($message['message']) . '</p></div>';
            }
            unset($_SESSION['webwijs']['messages']);
        }
        return $output;
    }
}
