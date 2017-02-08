<?php

namespace Webwijs\Dom;

/**
 * The RenderableInterface indicates that an object can render itself onto the screen of the user.
 *
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.0.0
 * @since 1.1.0
 */
interface RenderableInterface
{
    /**
     * Returns the HTML representation of this element.
     *
     * @return string the HTML representation of this element.
     */
    public function toHtml();
}
