<?php
namespace PublishPress\Permissions\Collab;

class Updated
{
    public function __construct($prev_version)
    {
        // single-pass do loop to easily skip unnecessary version checks
        do {
            if (!$prev_version) {
                break;  // no need to run through version comparisons if no previous version
            }

            if (version_compare($prev_version, '2.1.4-beta', '<')) {
                // added pp caps for exception assignment from Post/Term edit screen by non-Administrators
                if (!get_option('ppce_added_role_caps_21beta'))
                    Collab::populateRoles();
            }

        } while (0); // end single-pass version check loop
    }

    public static function populateRoles()
    {
        if ($role = @get_role('administrator')) {
            $role->add_cap('pp_set_edit_exceptions');
            $role->add_cap('pp_set_associate_exceptions');
            $role->add_cap('pp_set_term_assign_exceptions');
            $role->add_cap('pp_set_term_manage_exceptions');
            $role->add_cap('pp_set_term_associate_exceptions');
        }

        if ($role = @get_role('editor')) {
            $role->add_cap('pp_set_edit_exceptions');
            $role->add_cap('pp_set_associate_exceptions');
            $role->add_cap('pp_set_term_assign_exceptions');
            $role->add_cap('pp_set_term_manage_exceptions');
            $role->add_cap('pp_set_term_associate_exceptions');
        }

        update_option('ppce_added_role_caps_21beta', true);
    }
}
