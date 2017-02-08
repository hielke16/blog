<?php

namespace Webwijs\View\Helper;

class DropdownUsers
{
    public function dropdownUsers($args = null)
    {
        $defaults = array(
            'fields' => 'all_with_meta',
            'orderby' => 'login',
            'selected' => null,
            'name' => 'user_id',
        );
        $args = array_merge($defaults, (array) $args);
        $query = new WP_User_Query($args);
        $users = $query->get_results();

        ob_start();
        ?>
        <select name="<?php echo $args['name'] ?>">
            <option value="">--Selecteer</option>
            <?php foreach ($users as $user): ?>
            <option value="<?php echo $user->ID ?>" <?php selected($args['selected'], $user->ID) ?>><?php echo esc_attr($user->display_name) ?></option>
            <?php endforeach ?>
        </select>
        <?php
        return ob_get_clean();
    }
}
