<?php

if ('groups' == DATA::getGet('act') && (ManageRights::verifyRights('Droits et Permissions', 'Read', false, false) || ManageRights::verifyRights('Membres', 'Read', false, false))) {
    $groups = ManageRights::loadGroups();
    $data = [];
    if (empty($data)) {
        foreach ($groups as $group) {
            $id = $group->getId();
            $name = $group->getNameGroup();
            $data += [$id => $name];
        }
    }
    print_r(json_encode([
        'groups' => $data,
        'state' => true,
    ]));

    exit;
}
if ('perm_group' == DATA::getGet('act') && DATA::isGet('name') && ManageRights::verifyRights('Droits et Permissions', 'Read', false, false)) {
    $groupId = DATA::getGet('name');
    $database = new MYSQLPDO('graphene_bsm');
    $connexion = $database ->getConnexion();
    $requete = $connexion->prepare("SELECT *, modules_right.id FROM modules_right LEFT JOIN permissions ON (modules_right.id = module_id AND group_id = :groupId)");
    $requete->bindValue(':groupId', $groupId);
    $requete->execute();
    $perm_group = $requete->fetchAll();
    $data = [];
    foreach ($perm_group as $perm) {
      $data += [$perm['id'] => $perm['right_id']];
    }
    print_r(json_encode([
        'data' => $data,
        'state' => true
    ]));

    exit;
}

if ('perm_user' == DATA::getGet('act') && DATA::isGet('name') && ManageRights::verifyRights('Droits et Permissions', 'Read', false, false)) {
    $userId = DATA::getGet('name');
    $database = new MYSQLPDO('graphene_bsm');
    $connexion = $database ->getConnexion();
    $requete = $connexion->prepare("SELECT *, modules_right.id FROM modules_right LEFT JOIN permissions ON (modules_right.id = module_id AND user_id = :userId)");
    $requete->bindValue(':userId', $userId);
    $requete->execute();
    $perm_user = $requete->fetchAll();
    $data = [];

    foreach ($perm_user as $perm) {
      $data[$perm['id']] = $perm['right_id'];
    }

    foreach ($data as $key => $value) {
        if ($value === null) {
            $table = 'permissions';
            $table2 = 'grp_users';
            $filter = ["`{$table}`.module_id" => $key, "`{$table2}`.user_id" => $userId];
            $right = Generique::selectInner('groups', 'permissions', 'grp_users', 'group_id', 'graphene_bsm', $filter, 'right_id DESC');
            if (!empty($right)) {
                $data[$key] = $right[0]->getRightId();
            }
        }
    }
    print_r(json_encode([
        'data' => $data,
        'state' => true,
    ]));

    exit;
}
