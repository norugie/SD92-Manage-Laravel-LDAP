<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [
        'top',
        'person',
        'organizationalPerson',
        'user'
    ];

    /**
     * Retrieve the groups the user is apart of.
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'member');
    }
}
