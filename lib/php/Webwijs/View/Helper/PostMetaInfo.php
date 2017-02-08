<?php

namespace Webwijs\View\Helper;

class PostMetaInfo
{
    function PostMetaInfo()
    {
        return sprintf( __( 'Geplaatst op <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a>'),
            esc_url( get_permalink() ),
            esc_attr( get_the_time('', get_the_time()) ),
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() )
        );
    }
}
