<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class Group extends Model
{
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [];

    /**
     * Retrieve the members of the group.
     */
    public function members()
    {
        return $this->hasMany([
            Group::class, User::class
        ], 'memberof')->using($this, 'member');
    }
}
