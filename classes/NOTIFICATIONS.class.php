<?php

class NOTIFICATIONS
{
    public static function deleteNotification($id = false): void
    {
        if ($id == false) {
            Generique::delete("notification_user", "graphene_bsm", ['user_id' => USER::getId()]);
        } else {
            Generique::delete("notification_user", "graphene_bsm", ['id' => $id]);
        }
    }

    public static function resetNotificationNumber(): void
    {
        $data = ['view' => 1];
        Generique::update("notification_user", "graphene_bsm", ['view' => 0, 'user_id' => USER::getId()], $data);
    }

    public static function getNumberTotal(): int
    {
        $notif = Generique::select("notification_user", "graphene_bsm", ['view' => 0, 'user_id' => USER::getId()]);
        return sizeof($notif);
    }

    public static function getNotification(): array
    {
        $user_id = USER::getId();
        $right = MANAGERIGHTS::loadModulesRights();
        $notif = Generique::customSelect("notification", "graphene_bsm", "LEFT JOIN notification_user ON notification.id = notification_user.id_notification WHERE notification_user.user_id = {$user_id} ORDER BY notification.id desc");
        return $notif;
    }

    public static function CreateTag(int $userId): string
    {
        $account = Generique::selectOne('accounts', 'graphene_bsm', ['id_client' => $userId]);
        $avatar = UTILS::getAvatar($account->getPseudo());
        return "<span data-href='/Administration/Membres/{$account->getIdClient()}' class='notif-user-tag' title='Voir la fiche membre'><img src='{$avatar}' class='img-avatar'/>{$account->getNameOrPseudo()}</span>";
    }

    /**
     * Create a notification
     *
     * @param  string $icon
     * @param  string $message
     * @param  string $lien
     * @param  array $users array of users
     * @param  string|bool $module name of the module_rights
     * @return void
     */
    public static function add(string $icon, string $message, string $lien, array $users, $module = false): void
    {
        if ($module)
            $users = array_merge($users, ManageRights::getUsers($module));

        $users = array_unique($users);

        if (USER::isConnecte())
            $users = array_diff($users, [USER::getId()]);

        if (count($users) > 0) {
            $data = [
                'icon' => $icon,
                'message' => $message,
                'lien' => $lien
            ];
            Generique::insert("notification", "graphene_bsm", $data);
            // send notification through ntfy server
            $site = UTILS::getFunction('StaticUrl');
            $ntfyTopic = UTILS::getFunction('ntfy');
            if ($ntfyTopic == "") {
                $ntfyNew = "myG-NTFY".bin2hex(openssl_random_pseudo_bytes(4))."";
                MYSQL::query("UPDATE functions SET ntfy='".$ntfyNew."' WHERE id=1");
                $ntfyTopic = $ntfyNew;
            }
            file_get_contents('http://185.41.152.201:7777/'.$ntfyTopic, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: text/plain\r\n" . "Click: https://".$site.$lien."\r\n" . "Title: Notification de ".$site."\r\n" . "Tags: speaking_head",
                    'content' => ''.strip_tags($message).''
                ]
            ]));
            // continue rest of notification creation processing
            $id = Generique::selectMaxId("notification", "graphene_bsm");
            foreach($users as $user_id) {
                $data_user = [
                    'id_notification' => $id,
                    'view' => 0,
                    'user_id' => $user_id
                ];
                Generique::insert("notification_user", "graphene_bsm", $data_user);
            }
        }
    }
}
