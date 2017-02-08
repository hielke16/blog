<?php

namespace Webwijs\View\Helper;

use Webwijs\View;

class Comments
{
    protected $_initialized = false;

    public function comments()
    {
        if (!$this->_initialized) {
            $this->_init();
        }
        return $this;
    }

    public function haveComments()
    {
        return have_comments();
    }
    public function commentsOpen()
    {
        return comments_open();
    }

    public function renderForm($args=array())
    {
        $form = '';

        if(class_exists('RGForms') && get_option('theme_form_commentform')){
            $form = RGForms::get_form(get_option('theme_form_commentform'));
        }
        else{

            $defaults = array(
                'fields'               => array(),
                'comment_field'        => '<p class="comment-form-comment">' .
                                            '<textarea id="comment" placeholder="' . __('Vul hier uw bericht in...') . '" name="comment" class="comment-text required" cols="39" rows="8" tabindex="4" aria-required="true"></textarea>' .
                                          '</p>',
                'must_log_in'          => '',
                'logged_in_as'         => '',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
                'id_form'              => 'commentform',
                'id_submit'            => 'submit',
                'title_reply'          => '<h1>' . __( 'Leave a Reply' ) . '</h1>',
                'title_reply_to'       => __( 'Leave a Reply to %s' ),
                'cancel_reply_link'    => __( 'Cancel reply' ),
                'label_submit'         => __( 'Post Comment' ),
                'use_ajax'             => true
            );
            $args = array_merge($defaults, $args);
            ob_start();
            comment_form($args);
            $form = ob_get_clean();

            if($args['use_ajax']){
                wp_enqueue_script('jquery.validate', get_bloginfo('stylesheet_directory') . '/assets/lib/js/jquery.validate.js', array('jquery'));
                add_action('wp_footer', array($this, 'jsValidation'));
            }
        }

        return $form;
    }
    public function jsValidation()
    {
        ?>
        <script>
        jQuery(function($) {
            $('#commentform').validate({
                messages: {
                    comment: {
                        required: "<?php echo __('Dit veld is verplicht') ?>"
                    }
                }
            });
        });
        </script>
        <?php
    }
    public function renderComments()
    {
        $comments = '';
        if ($this->haveComments()) {
            ob_start();
            wp_list_comments(array('callback' => array($this, 'renderComment')));
            $comments = ob_get_clean();
        }
        return $comments;
    }
    public function renderComment($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        $view = new View();
        echo $view->partial('partials/comment/list-item.phtml', array('comment' => $comment, 'args' => $args, 'depth' => $depth));
    }
    public function paginate()
    {
       return $this->view->paginate(array(), 'comments');
    }
    protected function _init()
    {
        global $wp_query, $post, $wpdb, $user_ID;

        $comments = get_comments(array('post_id' => $post->ID, 'status' => 'approve', 'order' => 'DESC'));

        $wp_query->comments = $comments;
        $wp_query->comment_count = count($wp_query->comments);
        update_comment_cache($wp_query->comments);
    }
}
