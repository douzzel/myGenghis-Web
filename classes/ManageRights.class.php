<?php

class ManageRights
{
    public static function loadGroups()
    {
        return Generique::select('groups', 'graphene_bsm');
    }

    public static function loadUsers()
    {
        return Generique::select('accounts', 'graphene_bsm');
    }

    public static function loadRights()
    {
        return Generique::select('rights', 'graphene_bsm');
    }

    public static function loadModulesRights()
    {
        return Generique::select('modules_right', 'graphene_bsm');
    }

    public static function GenerateFormGroup()
    {
        return [['name_group', 'text', 'Groupe', 'Nom du groupe', 'messagerie_nameGroupe']];
    }

    public static function GenerateFormManageGroup()
    {
        $groups = self::loadGroups();
        $users = self::loadUsers();

        return [['id_group', 'select', 'Groupe', 'Sélectionner un groupe', $groups, 'groupe', 'messagerie_id_group'],
            ['id_user', 'select', 'Utilisateur', 'Sélectionner un utilisateur ',  $users, 'user', 'messagerie_id_user'],
        ];
    }

    public static function AddGroupe()
    {
        $message = '';
        if (isset($_POST['name_group'])) {
            if (!empty($_POST['name_group'])) {
                $date = new DateTime();
                $data = ['name_group' => $_POST['name_group'], 'gbsm_fixed_group' => $_POST['fixer'], 'date_created' => $date->format('Y-m-d H:i:s')];
                Generique::insert('groups', 'graphene_bsm', $data);
                $message = 'Le groupe a été ajouté';
                echo json_encode([
                    'message' => $message,
                    'state' => true,
                ]);

                exit;
            }
            $message = 'Ce champ est obligatoire !';
            echo json_encode([
                'message' => $message,
                'state' => false,
            ]);

            exit;
        }
    }

    public static function AddUserToGroupe()
    {
        $message = '';
        if (isset($_POST['id_group'])) {
            if (!empty($_POST['id_group']) && !empty($_POST['id_user'])) {
                $date = new DateTime();
                $data = ['group_id' => $_POST['id_group'], 'user_id' => $_POST['id_user'], 'gbsm_fixed_perm' => $_POST['gbsm_fixed_group_user'], 'date_created' => $date->format('Y-m-d H:i:s')];
                Generique::insert('grp_users', 'graphene_bsm', $data);
                $message = 'L\'utilisateur  a été rajouté au groupe';
                echo json_encode([
                    'message' => $message,
                    'state' => true,
                ]);

                exit;
            }
            if (empty($_POST['id_group'])) {
                $message = 'Ce champ est obligatoire !';
                echo json_encode([
                    'message' => $message,
                    'fieldgroupe' => true,
                    'state' => false,
                ]);

                exit;
            }
            if (empty($_POST['id_user'])) {
                $message = 'Ce champ est obligatoire !';
                echo json_encode([
                    'message' => $message,
                    'fielduser' => true,
                    'state' => false,
                ]);

                exit;
            }
            $message = 'Ce champ est obligatoire !';
            echo json_encode([
                'message' => $message,
                'fieldgroupe' => true,
                'fielduser' => true,
                'state' => false,
            ]);

            exit;
        }
    }

    public static function SetUserGroups()
    {
        $message = '';
        if (DATA::isPost('editGroup')) {
            if (!empty($_POST['id_user'])) {
                $filter = ['user_id' => $_POST['id_user']];
                Generique::delete('grp_users', 'graphene_bsm', $filter);
                if (isset($_POST['id_user'])) {
                    $date = new DateTime();
                    if (!empty($_POST['id_group'])) {
                        foreach ($_POST['id_group'] as $group) {
                            $data = ['group_id' => $group, 'user_id' => $_POST['id_user'], 'gbsm_fixed_perm' => 0, 'date_created' => $date->format('Y-m-d H:i:s')];
                            Generique::insert('grp_users', 'graphene_bsm', $data);
                        }
                    }
                }
                $message = 'Les groupes de l\'utilisateur ont été mis à jour';
                echo json_encode([
                    'message' => $message,
                    'state' => true,
                ]);

                exit;
            }
            $message = 'Ce champ est obligatoire !';
            echo json_encode([
                'message' => $message,
                'fieldgroupe' => true,
                'fielduser' => true,
                'state' => false,
            ]);

            exit;
        }
    }


    public static function SetUserPersona()
    {
        $message = '';
        if (DATA::isPost('editPersona')) {
            if (!empty($_POST['id_user'])) {
                $filter = ['accounts_id' => $_POST['id_user']];
                Generique::delete('contacts_persona', 'graphene_bsm', $filter);
                if (isset($_POST['id_user'])) {
                    if (!empty($_POST['id_persona'])) {
                        foreach ($_POST['id_persona'] as $persona) {
                            $data = ['persona_id' => $persona, 'accounts_id' => $_POST['id_user']];
                            Generique::insert('contacts_persona', 'graphene_bsm', $data);
                        }
                    }
                }
                $message = 'Les personas de l\'utilisateur ont été mis à jour';
                echo json_encode([
                    'message' => $message,
                    'state' => true,
                ]);

                exit;
            }
            $message = 'Ce champ est obligatoire !';
            echo json_encode([
                'message' => $message,
                'fieldgroupe' => true,
                'fielduser' => true,
                'state' => false,
            ]);

            exit;
        }
    }

    public static function AddPermission()
    {
        if (isset($_POST['is_group'])) {
            $fixer = 0;
            $data = [];
            $date = new DateTime();
            if (isset($_POST['gbsm_fixed_perm'])) {
                $fixer = $_POST['gbsm_fixed_perm'];
            }
            if (0 == $_POST['is_group']) {
                $data = ['user_id' => $_POST['user_id'],
                'group_id' => null,
                'right_id' => $_POST['right_id'],
                'gbsm_fixed_perm' => $fixer,
                'module_id' => $_POST['module_id'],
                'date_created' => $date->format('Y-m-d H:i:s')];
                $filter = ['user_id' => $_POST['user_id'], 'module_id' => $_POST['module_id']];
            } else {
                $data = ['user_id' => null,
                'group_id' => $_POST['group_id'],
                'right_id' => $_POST['right_id'],
                'gbsm_fixed_perm' => $fixer,
                'module_id' => $_POST['module_id'],
                'date_created' => $date->format('Y-m-d H:i:s')];
                $filter = ['group_id' => $_POST['group_id'], 'module_id' => $_POST['module_id']];
            }

            Generique::delete('permissions', 'graphene_bsm', $filter);
            Generique::insert('permissions', 'graphene_bsm', $data);
            UTILS::notification('success', 'Droits mis à jour', false, true);
        }
    }

    public static function verifyRights($module, $rightRequest, $redirect = true, $message = true)
    {
        if (!USER::isConnecte()) {
            return false;
        }

        $filterAccount = ['pseudo' => $_SESSION['login']];
        $currentAccount = Generique::selectOne('accounts', 'graphene_bsm', $filterAccount);

        // Disconnect the user if the account doesn't exist
        if (!$currentAccount) {
            header('Location: /Deconnexion');
            exit;
        }
        $filterGroups = ['user_id' => $currentAccount->getIdClient()];
        $groupsOfCurrentAccount = Generique::select('grp_users', 'graphene_bsm', $filterGroups);
        $filterModule = ['name_module' => $module];
        $currentModule = Generique::selectOne('modules_right', 'graphene_bsm', $filterModule);
        $filterRightAccount = ['user_id' => $currentAccount->getIdClient(), 'module_id' => $currentModule->getId()];
        $rightCurrentAccount = Generique::selectOne('permissions', 'graphene_bsm', $filterRightAccount);

        // if user dont have any group_user
        if (empty($groupsOfCurrentAccount) || $rightCurrentAccount) {
            if ($rightCurrentAccount && $rightCurrentAccount->getRightId()) {
                return self::checkRights(['id' => $rightCurrentAccount->getRightId()], $rightRequest, $redirect, $message);
            }
            return self::redirectToHome($redirect, $message);
        } else {
            $table = 'permissions';
            $table2 = 'grp_users';
            $filter = ["`{$table}`.module_id" => $currentModule->getId(), "`{$table2}`.user_id" => $currentAccount->getIdClient()];
            $right = Generique::selectInner('groups', 'permissions', 'grp_users', 'group_id', 'graphene_bsm', $filter, 'right_id DESC');

            if (!empty($right)) {
                return self::checkRights(['id' => $right[0]->getRightId()], $rightRequest, $redirect, $message);
            }

            return self::redirectToHome($redirect, $message);
        }
    }

    private static function checkRights($filterRight, $rightRequest, $redirect, $message)
    {
        $currentRight = Generique::select('rights', 'graphene_bsm', $filterRight);
        if ($currentRight && (!$currentRight[0] || ('Read And Write' != $currentRight[0]->getLabel() && $rightRequest != $currentRight[0]->getLabel()))) {
            return self::redirectToHome($redirect, $message);
        }

        return true;
    }

    private static function redirectToHome($redirect, $message)
    {
        if ($redirect) {
            UTILS::notification('warning', "Vous n'avez pas l'autorisation d'accéder à cette page", false);
            if (isset($_SERVER['HTTP_REFERER']))
                header('location: '.$_SERVER['HTTP_REFERER']);
            else if (!isset($_SERVER['HTTP_REFERER']))
                header('Location: /Account');
            else
                header('Location: /Connexion');

            exit;
        } elseif ($message) {
            UTILS::notification('warning', "Vous n'avez pas l'autorisation d'effectuer cette action", false);
        }

        return false;
    }

        /**
     * getUsersFromGroup get all users of a group
     *
     * @param  int $group Id of the group
     * @return array Array of users
     */
    public static function getUsersFromGroup(int $group): array {
        $users = [];
        $grp_users = Generique::select('grp_users', 'graphene_bsm', ['group_id' => $group]);
        foreach ($grp_users as $user) {
            $users[] = $user->getUserId();
        }
        return $users;
    }

    /**
     * getUsers return a array of users with Read or Read And Write rights on a module
     *
     * @param  string $module Name of the module
     * @return array Array of users
     */
    public static function getUsers(string $module): array {
        $currentModule = Generique::selectOne('modules_right', 'graphene_bsm', ['name_module' => $module]);

        $users = [];
        // Get all permissions for module and add users and groups to array
        $persmissions = Generique::customSelect('permissions', 'graphene_bsm', "WHERE right_id IN (1, 3) AND module_id = '{$currentModule->getId()}'");
        foreach ($persmissions as $perm) {
            if ($perm->getUserId() && $perm->getUserId()) {
                $users[] = $perm->getUserId();
            } else if ($perm->getGroupId()) {
                $users = array_merge($users, self::getUsersFromGroup($perm->getGroupId()));
            }
        }

        // remove duplicates
        $users = array_unique($users);
        sort($users);

        // Remove users where permissions is not allowed
        $noPerms = Generique::select('permissions', 'graphene_bsm', ['right_id' => 0, 'module_id' => $currentModule->getId()]);
        $noPermsUsers = [];
        foreach ($noPerms as $noPerm) {
            $noPermsUsers[] = $noPerm->getUserId();
        }
        return array_diff($users, $noPermsUsers);
    }
}
