<?php
ManageRights::verifyRights('Droits et Permissions', 'Read');
$writePermission = ManageRights::verifyRights('Droits et Permissions', 'Write', false, false);
if (!$writePermission) {
  $DISABLE_FORM = true;
}

if ($writePermission) {
  if (!empty($_POST['name_group'])) {
      $date = new DateTime();
      $data = ['name_group' => $_POST['name_group'], 'gbsm_fixed_group' => 1, 'date_created' => $date->format('Y-m-d H:i:s')];
      Generique::insert('groups', 'graphene_bsm', $data);
      $message = 'Le groupe a été ajouté';
      UTILS::notification('success', $message, false);
      UTILS::addHistory(USER::getPseudo(), 51, 'Nouveau groupe "'.$_POST['name_group'].'" créé');
  }

  ManageRights::AddPermission();
  ManageRights::AddUserToGroupe();
}

$groups = ManageRights::loadGroups();
$users =  ManageRights::loadUsers();
$rights = ManageRights::loadRights();
$modules = ManageRights::loadModulesRights();
$inputs = ManageRights::GenerateFormGroup();
$manage_groups_forms = ManageRights::GenerateFormManageGroup();
$url = $_SERVER["REQUEST_URI"];

array_shift($groups);
array_shift($users);

$noRights = new EntityRights;
$noRights->setLabel('Aucun');
$noRights->setId('0');
$rights[] = $noRights;

$listMenuArray = [
  ['Paramètres myGenghis', '/Administration/Settings'],
  ['Droits et permissions', '', true],
];
$PAGES = $twig->render('_admin/componants/Rights/manageRights.html.twig',[
    'groups' => $groups,
    'users' => $users,
    'rights' => $rights,
    'modules' => $modules,
    'inputs' => $inputs,
    'url' => $url,
    'manage_groups_forms' => $manage_groups_forms,
    'FIL_ARIANNE' => MENU::filArianne($listMenuArray, 'settings'),
  ]);

$TITRE = "Gérer les droits et permissions";
$DESCRIPTION = "Centre de gestion des droits et permissions";
?>
