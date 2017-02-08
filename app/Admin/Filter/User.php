<?php

namespace Theme\Admin\Filter;

class User
{
    public static function editableRoles($roles)
    {
        unset($roles['subscriber']);
        if (!current_user_can('update_core')) {
            unset($roles['administrator']);
        }
        return $roles;
    }
}
