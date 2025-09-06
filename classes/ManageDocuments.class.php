<?php

class NameAccess {
    private string $name;
    public EntityDocumentsAccess $access;

    public function __construct(EntityDocumentsAccess $access, string $name) {
        $this->name = $name;
        $this->access = $access;
    }

    public function getName()
    {
        return html_entity_decode($this->name);
    }
}


class ManageDocuments
{
    public static function saveDocument()
    {
        if (DATA::isPost('title')) {
            $title = DATA::getPost('title');
            $data = ['title' => $title, 'content' => DATA::getPost('content')];
            if (DATA::isPost('document_id')) {
                $documentId = DATA::getPost('document_id');
                $filter = ['id' => $documentId];
                $data['updated_at'] = date("Y-m-d H:i:s");
                Generique::update('documents', 'graphene_bsm', $filter, $data);

                UTILS::addHistory(USER::getPseudo(), 24, "Document {$title} modifié");
                $filter = ['document_id' => $documentId];
                Generique::delete('documents_access', 'graphene_bsm', $filter);
            } else {
                $data['owner'] = USER::getId();

                if ($_SERVER['HTTP_REFERER']) {
                    $query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
                    if ($query) {
                        parse_str($query, $output);
                        if (isset($output['folder']))
                            $data['folder'] = $output['folder'];
                        if (isset($output['section']))
                            $data['section_id'] = $output['section'];
                        if (!isset($output['folder']) && !isset($output['section'])) {
                            $data['section_id'] = MYSQL::selectOneValue("SELECT min(id) FROM documents_sections");
                        }
                    } else {
                        $data['section_id'] = MYSQL::selectOneValue("SELECT min(id) FROM documents_sections");
                    }
                }
                Generique::insert('documents', 'graphene_bsm', $data);
                UTILS::addHistory(USER::getPseudo(), 24, "Nouveau document $title");
                $documentId = Generique::selectMaxId('documents', 'graphene_bsm');
            }

            $users = [];
            if (DATA::isPost('user_share')) {
                $userShare = array_filter(explode(',', DATA::getPost('user_share')));
                $users = array_merge($users, $userShare);
                foreach ($userShare as $share) {
                    $data = ['document_id' => $documentId, 'user_id' => $share];
                    Generique::insert('documents_access', 'graphene_bsm', $data);
                }
            }
            if (DATA::isPost('group_share')) {
                $groupShare = array_filter(explode(',', DATA::getPost('group_share')));
                foreach ($groupShare as $share) {
                    $users = array_merge($users, ManageRights::getUsersFromGroup($share));
                    $data = ['document_id' => $documentId, 'group_id' => $share];
                    Generique::insert('documents_access', 'graphene_bsm', $data);
                }
            }
            if (DATA::isPost('persona_share')) {
                $personaShare = array_filter(explode(',', DATA::getPost('persona_share')));
                foreach ($personaShare as $share) {
                    $data = ['document_id' => $documentId, 'persona_id' => $share];
                    Generique::insert('documents_access', 'graphene_bsm', $data);
                }
            }
            NOTIFICATIONS::add('folder', "Document <b>{$title}</b> partagé avec vous", "/Administration/Documents/View/{$documentId}", array_unique($users), false);

            if (DATA::isPost('createWebLink')) {
                $filter = ['id' => DATA::getPost('document_id')];
                $document = Generique::selectOne('documents', 'graphene_bsm', $filter);
                $data = ['weblink' => $document->getTitle()];
                if (!Generique::selectOne('documents', 'graphene_bsm', $data)) {
                    Generique::update('documents', 'graphene_bsm', $filter, $data);
                } else {
                    $data = ['weblink' => bin2hex(openssl_random_pseudo_bytes(16))];
                    if (!Generique::selectOne('documents', 'graphene_bsm', $data)) {
                        Generique::update('documents', 'graphene_bsm', $filter, $data);
                    } else {
                        $data = ['weblink' => bin2hex(openssl_random_pseudo_bytes(16))];
                        if (!Generique::selectOne('documents', 'graphene_bsm', $data)) {
                            Generique::update('documents', 'graphene_bsm', $filter, $data);
                        }
                    }
                }

                $message = 'Le lien a été créé';
                echo json_encode([
                    'message' => $message,
                    'state' => true,
                    'redirect' => "/Administration/Documents/Edit/{$documentId}",
                ]);

                exit;
            }

            if (DATA::isPost('weblink')) {
                $filter = ['weblink' => DATA::getPost('weblink')];
                $doc = Generique::selectOne('documents', 'graphene_bsm', $filter);
                if (!$doc || ($doc && $doc->getId() !== $documentId)) {
                    $data = $filter;
                    $filter = ['id' => $documentId];
                    Generique::update('documents', 'graphene_bsm', $filter, $data);
                }
            }

            if ($attachments = self::uploadFiles()) {
                $filter = ['id' => $documentId];
                $doc = Generique::selectOne('documents', 'graphene_bsm', $filter);
                if ($attach = $doc->getAttachments()) {
                    $data = ['attachments' => serialize(array_merge(unserialize($attach), $attachments))];
                } else {
                    $data = ['attachments' => serialize($attachments)];
                }
                Generique::update('documents', 'graphene_bsm', $filter, $data);
            }

            $message = 'Le document a été sauvegardé';
            echo json_encode([
                'message' => $message,
                'state' => true,
                'redirect' => "/Administration/Documents/View/{$documentId}",
            ]);

            exit;
        }
    }

    public static function verifyRights(string $type, int $id)
    {
        if (!USER::isConnecte()) {
            return false;
        }

        $userId = USER::getId();

        $filter = ['id' => $id, 'owner' => USER::getId()];
        if ($type == 'document')
            $ownerRights = Generique::selectOne('documents', 'graphene_bsm', $filter);
        else if ($type == 'folder')
            $ownerRights = Generique::selectOne('documents_folders', 'graphene_bsm', $filter);
        if ($ownerRights) {
            return true;
        }

        if ($type == 'document')
            $filter = ['document_id' => $id, 'user_id' => $userId];
        else if ($type == 'folder')
            $filter = ['folder_id' => $id, 'user_id' => $userId];
        $userRights = Generique::selectOne('documents_access', 'graphene_bsm', $filter);
        if ($userRights) {
            return true;
        }

        if ($type == 'document')
            $filter = ['document_id' => $id];
        else if ($type == 'folder')
            $filter = ['folder_id' => $id];
        $documentGroup = Generique::select('documents_access', 'graphene_bsm', $filter);

        $groupsRights = [];
        foreach ($documentGroup as $dG) {
            $group = $dG->getGroup_id();
            if ($group) {
                $groupsRights[] = $group;
            }
        }

        $filterGroups = ['user_id' => $userId];
        $groupsOfCurrentAccount = Generique::select('grp_users', 'graphene_bsm', $filterGroups);
        foreach ($groupsOfCurrentAccount as $group) {
            if (in_array($group->getGroup_id(), $groupsRights)) {
                return true;
            }
        }

        return false;
    }

    public static function listAccess($filter) {
        $accesses = Generique::select('documents_access', 'graphene_bsm', $filter);

        $listAccess = [];
        foreach ($accesses as $access) {
            if ($access->getUser_id()) {
                $filter = ['id_client' => $access->getUser_id()];
                $user = Generique::selectOne('accounts', 'graphene_bsm', $filter);
                if ($user) {
                    $listAccess[] = new NameAccess($access, $user->getNameOrPseudo());
                }
            } else if ($access->getGroup_id()) {
                $filter = ['id' => $access->getGroup_id()];
                $group = Generique::selectOne('groups', 'graphene_bsm', $filter);
                if ($group) {
                    $listAccess[] = new NameAccess($access, $group->getNameGroup());
                }
            } else if ($access->getPersona_id()) {
                $filter = ['id' => $access->getPersona_id()];
                $persona = Generique::selectOne('persona', 'graphene_bsm', $filter);
                if ($persona) {
                    $listAccess[] = new NameAccess($access, $persona->getName());
                }
            }
        }
        return $listAccess;
    }

    private static function uploadFiles()
    {
        $attachments = null;
        $files = [];
        if (isset($_FILES['attachments']))
        foreach ($_FILES['attachments'] as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files)) {
                    $files[$i] = [];
                }
                $files[$i][$k] = $v;
            }
        }

        include 'classes/upload.class.php';
        if (isset($files)) {
            $attachments = [];
            foreach ($files as $file) {
                $handle = new upload($file, 'fr_FR');
                if ($handle->uploaded) {
                    $handle->process('uploads/documents');
                    if ($handle->processed) {
                        $attachments[] = ['uploads/documents/'.$handle->file_dst_name => $handle->file_dst_name];
                    }
                }
            }
        }

        return $attachments;
    }

    public static function moveFolder() {
        if (DATA::isPost('moveFolder')) {
            if (ManageDocuments::verifyRights('folder', (int)DATA::getPost('moveFolder'))) {
                $folderId = DATA::getPost('moveFolder');
                $folderName = MYSQL::selectOneValue("SELECT `name` FROM documents_folders WHERE id = '{$folderId}'");

                if (DATA::isPost('toFolder')) {
                    $filter = ['id' => $folderId];
                    $data = ['parent' => DATA::getPost('toFolder'), 'section_id' => null];
                    Generique::update('documents_folders', 'graphene_bsm', $filter, $data);
                    $toFolderName = MYSQL::selectOneValue('SELECT name FROM documents_folders WHERE id = '.DATA::getPost('toFolder'));
                    UTILS::addHistory(USER::getPseudo(), 41, "Dossier “{$folderName}” déplacé dans le dossier “{$toFolderName}”", "/Administration/Documents/Folders/{$folderId}");
                    exit;
                } elseif (DATA::isPost('toSection')) {
                    $filter = ['id' => $folderId];
                    $data = ['section_id' => DATA::getPost('toSection'), 'parent' => null];
                    Generique::update('documents_folders', 'graphene_bsm', $filter, $data);
                    $toSectionName = MYSQL::selectOneValue('SELECT name FROM documents_sections WHERE id = '.DATA::getPost('toSection'));
                    UTILS::addHistory(USER::getPseudo(), 41, "Dossier “{$folderName}” déplacé dans la section “{$toSectionName}”", "/Administration/Documents/Folders/{$folderId}");
                    exit;
                }
            } else {
                http_response_code(403);
                exit;
            }
        }
    }

    public static function moveLink() {
        if (DATA::isPost('moveLink')) {
            $linkId = DATA::getPost('moveLink');
            $linkName = MYSQL::selectOneValue("SELECT `name` FROM documents_links WHERE id = '$linkId}'");
            $linkHref = MYSQL::selectOneValue("SELECT `href` FROM documents_links WHERE id = '$linkId}'");

            if (DATA::isPost('toFolder')) {
                $filter = ['id' => $linkId];
                $data = ['folder' => DATA::getPost('toFolder'), 'section_id' => null];
                Generique::update('documents_links', 'graphene_bsm', $filter, $data);
                $toFolderName = MYSQL::selectOneValue('SELECT name FROM documents_folders WHERE id = '.DATA::getPost('toFolder'));
                UTILS::addHistory(USER::getPseudo(), 54, "Lien “{$linkName}” déplacé dans le dossier “{$toFolderName}”", $linkHref);
                exit;
            } else if (DATA::isPost('toSection')) {
                $filter = ['id' => $linkId];
                $data = ['section_id' => DATA::getPost('toSection'), 'folder' => null];
                Generique::update('documents_links', 'graphene_bsm', $filter, $data);
                $toSectionName = MYSQL::selectOneValue('SELECT name FROM documents_sections WHERE id = '.DATA::getPost('toSection'));
                UTILS::addHistory(USER::getPseudo(), 54, "Lien “{$linkName}” déplacé dans la section “{$toSectionName}”", $linkHref);
                exit;
            }
        }
    }

    public static function moveFile() {
        if (DATA::isPost('moveFile')) {
            $fileId = DATA::getPost('moveFile');
            $fileName = MYSQL::selectOneValue("SELECT `name` FROM documents_files WHERE id = '{$fileId}'");

            if (DATA::isPost('toFolder')) {
                $filter = ['id' => $fileId];
                $data = ['folder' => DATA::getPost('toFolder'), 'section_id' => null];
                Generique::update('documents_files', 'graphene_bsm', $filter, $data);
                $toFolderName = MYSQL::selectOneValue('SELECT name FROM documents_folders WHERE id = '.DATA::getPost('toFolder'));
                UTILS::addHistory(USER::getPseudo(), 24, "Document “{$fileName}” déplacé dans le dossier “{$toFolderName}”", "/Administration/Documents/View/{$fileId}");
                exit;
            } elseif (DATA::isPost('toSection')) {
                $filter = ['id' => $fileId];
                if (DATA::getPost('toSection') == 'NULL') {
                    $data = ['section_id' => null, 'folder' => null];
                } else {
                    $data = ['section_id' => DATA::getPost('toSection'), 'folder' => null];
                }
                Generique::update('documents_files', 'graphene_bsm', $filter, $data);
                $toSectionName = MYSQL::selectOneValue('SELECT name FROM documents_sections WHERE id = '.DATA::getPost('toSection'));
                UTILS::addHistory(USER::getPseudo(), 24, "Document “{$fileName}” déplacé dans la section “{$toSectionName}”", "/Administration/Documents/View/{$fileId}");
                exit;
            }
        }
    }

    public static function moveDocument() {
        if (DATA::isPost('moveDocument')) {
            if (ManageDocuments::verifyRights('document', (int)DATA::getPost('moveDocument'))) {
                $docId = DATA::getPost('moveDocument');
                $documentName = MYSQL::selectOneValue("SELECT `title` FROM documents WHERE id = '{$docId}'");

                if (DATA::isPost('toFolder')) {
                    $filter = ['id' => $docId];
                    $data = ['folder' => DATA::getPost('toFolder'), 'section_id' => null];
                    Generique::update('documents', 'graphene_bsm', $filter, $data);
                    $toFolderName = MYSQL::selectOneValue('SELECT name FROM documents_folders WHERE id = '.DATA::getPost('toFolder'));
                    UTILS::addHistory(USER::getPseudo(), 24, "Document “{$documentName}” déplacé dans le dossier “{$toFolderName}”", "/Administration/Documents/View/{$docId}");
                    exit;
                } elseif (DATA::isPost('toSection')) {
                    $filter = ['id' => $docId];
                    if (DATA::getPost('toSection') == 'NULL') {
                        $data = ['section_id' => null, 'folder' => null];
                    } else {
                        $data = ['section_id' => DATA::getPost('toSection'), 'folder' => null];
                    }
                    Generique::update('documents', 'graphene_bsm', $filter, $data);
                    $toSectionName = MYSQL::selectOneValue('SELECT name FROM documents_sections WHERE id = '.DATA::getPost('toSection'));
                    UTILS::addHistory(USER::getPseudo(), 24, "Document “{$documentName}” déplacé dans la section “{$toSectionName}”", "/Administration/Documents/View/{$docId}");
                    exit;
                }
            } else {
                http_response_code(403);
                exit;
            }
        }
    }

    public static function search() {
        if (DATA::isPost('search')) {
            $search = html_entity_decode(DATA::getPost('search'));
            $list = [];
            $filter = "LEFT JOIN accounts ON documents.owner = accounts.id_client WHERE title LIKE '%{$search}%' OR Pseudo LIKE '%{$search}%' OR Prenom LIKE '%{$search}%' OR Nom LIKE '%{$search}%'";
            $documents = Generique::customSelect('documents', 'graphene_bsm', $filter);
            foreach ($documents as $document) {
                $date = UTILS::date($document->getCreatedAt(), 'd/m/Y');
                $list[] = ['label' => "{$document->getTitle()} - {$document->getOwnerName()} [{$date}]", 'href' => 'Documents/View/'.$document->getId()];
            }
            $filter = "WHERE name LIKE '%{$search}%'";
            $folders = Generique::customSelect('documents_folders', 'graphene_bsm', $filter);
            foreach ($folders as $folder) {
                $list[] = ['label' => $folder->getName(), 'href' => 'Documents/Folders/'.$folder->getId()];
            }
            usort($list, function($a, $b) {return strcmp($a['label'], $b['label']);});
            echo json_encode($list);
            exit;
        }
    }

    private static function unlinkFolder(string $path) {
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($path."/".$object) == "dir")
                    self::unlinkFolder($path."/".$object);
                else
                    unlink($path."/".$object);
            }
            }
            reset($objects);
            rmdir($path);
        }
    }

    public static function deleteFolder(int $folderId) {
        // delete documents
        Generique::delete('documents', 'graphene_bsm', ['folder' => $folderId]);

        // delete documents_files and folder
        Generique::delete('documents_files', 'graphene_bsm', ['folder' => $folderId]);
        self::unlinkFolder($_SERVER["DOCUMENT_ROOT"]."/uploads/Drive/{$folderId}");

        // delete sub folders
        $sub_folders = Generique::select('documents_folders', 'graphene_bsm', ['parent' => $folderId]);
        foreach ($sub_folders as $sub_folder) {
            self::deleteFolder($sub_folder->getId());
        }
        Generique::delete('documents_folders', 'graphene_bsm', ['id' => $folderId]);
    }

    public static function deleteSection(int $sectionId) {
        // delete documents
        Generique::delete('documents', 'graphene_bsm', ['section_id' => $sectionId]);

        // delete documents_files
        Generique::delete('documents_files', 'graphene_bsm', ['section_id' => $sectionId]);

        // delete documents_folders and sub folders
        $folders = Generique::select('documents_folders', 'graphene_bsm', ['section_id' => $sectionId]);
        foreach ($folders as $folder) {
            self::deleteFolder($folder->getId());
        }

        // delete section
        Generique::delete('documents_sections', 'graphene_bsm', ['id' => $sectionId]);
        self::unlinkFolder($_SERVER["DOCUMENT_ROOT"]."/uploads/Drive/sect-{$sectionId}");
    }
}
