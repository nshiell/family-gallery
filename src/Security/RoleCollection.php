<?php

namespace App\Security;

final class RoleCollection
{
    const ROLE_USER  = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_TITLE_USER  = 'User';
    const ROLE_TITLE_ADMIN = 'Administrator';

    public function getRolesKeyedOnTitleNoUser()
    {
        return [
            self::ROLE_TITLE_ADMIN => self::ROLE_ADMIN
        ];
    }
}
