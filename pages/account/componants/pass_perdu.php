<?php
    $tplcompte = new Template;
    $tplcompte->setFile('compte', './account/componants/pass_perdu.html');

    $tplcompte->value('URL', $_SERVER['REQUEST_URI']);

    if(!USER::isConnecte()){
        $tplcompte->bloc('IF_PAS_CONNECTE');

        $listMenuArray = array(
            array('Connexion', '/Connexion'),
            array('Mot de passe perdu ?', '/Pass', true),
        );

        $tplcompte->values(array(
            'FIL_ARIANNE' =>  MENU::filArianne($listMenuArray)
        ));

        if(DATA::isGet('RestorePassword')){
            $value = explode('::', base64_decode(DATA::getGet('RestorePassword')));
            $expiration = date('Hi', time()) - date('Hi', $value[1]);
            $reqAccount = MYSQL::query('SELECT * FROM accounts WHERE Email = \''.$value[2].'\' AND Password = \''.$value[0].'\' AND RestorePassword = 1');
            if(mysqli_num_rows($reqAccount) > 0){

                if($expiration < 10){
                    $tplcompte->bloc('IF_PAS_CONNECTE.NEW_PASSWORD');
                    if(DATA::isPost('password') && DATA::isPost('passwordCheck')){
                        $type = 'warning';
                        if(DATA::getPost('password') === DATA::getPost('passwordCheck')){
                            // if(md5(DATA::getPost('captcha')) == $_SESSION['image_random_value']){
                              $filter = ['Email' => $value[2], 'Password' => $value[0]];
                              $data = ['Password' => hash('sha256', DATA::getPost('password')), 'RestorePassword' => 0];
                                Generique::update('accounts', 'graphene_bsm', $filter, $data);

                                /*
                                // check if a NC account exists under the same pseudo
                                $ncAccountCheck = NCMYSQL::query("SELECT * FROM oc_accounts WHERE uid = '".$reqAccount->Pseudo."'");
                                if (mysqli_num_rows($ncAccountCheck) > 0) {
                                    // update user's NC details
                                    $ncusername = $reqAccount->Pseudo;
                                    $ncuserpass = DATA::getPost('password');
                                    nupa::editUser($ncusername, "password", $ncuserpass);
                                }
                                else {
                                    // if pseudos are not the same, look for a NC account that has the same email address and retrieve pseudo from it
                                    $ncFetchAccount = NCMYSQL::query('SELECT uid FROM oc_accounts WHERE data LIKE \'%"email":{"value":"'.$value[2].'",%\'');
                                    if (mysqli_num_rows($ncFetchAccount) > 0) {
                                        // update user's NC details
                                        $ncusername = $ncFetchAccount->uid;
                                        $ncuserpass = DATA::getPost('password');
                                        nupa::editUser($ncusername, "password", $ncuserpass);
                                    }
                                }
                                */

                                // HumhHub update password change
                                $hhFile = "./classes/humhub";
                                if(is_dir($hhFile)) {
                                    hupa::passwordReset($reqAccount->Pseudo, DATA::getPost('password'));
                                }
                                // update PHP-Calendar account password
                                $pcFile = "./classes/php-calendar";
                                if(is_dir($pcFile)) {
                                    cupa::mod($reqAccount->Pseudo, DATA::getPost('password'));
                                }
                                // update PHProject account password
                                $ppFile = "./classes/phproject";
                                if(is_dir($pcFile)) {
                                    pupa::mod($reqAccount->Pseudo, "", "", DATA::getPost('password'));
                                }

                                $type = 'success';
                                $messages = '<div>Le mot de passe a été réinitialisé avec succès. <span redir="true" class="text-dark">[Redirection dans 3 secondes...]</span></div>';
                                $data['redir'] = 'true';
                            // }else{
                            //     $messages = 'Merci de recopier correctement l\'image de verification.';
                            // }
                        }else{
                            $messages = 'Vos deux mots de passe ne sont pas identiques...';
                        }

                        $data = '<div class="notification">';
                        $data .= '<div class="alert alert-'.$type.' w-50 d-flex align-items-center justify-content-between" role="alert">';
                        $data .= $messages;
                        $data .= '</div>';
                        $data .= '</div>';

                        die(json_encode($data));

                    }

                }else{
                    UTILS::notification('warning', 'Désolé mais le délais est dépassé.', false, true);
                    header('location: /Pass');
                    exit;
                }

            }else{
                UTILS::notification('warning', 'Désolé l\'url n\'est pas valide.', false, true);
                header('location: /Pass');
                exit;
            }

        }else{
            $tplcompte->bloc('IF_PAS_CONNECTE.RESTORE_EMAIL');
            if(DATA::isPost('email')){
                $reqAccount = MYSQL::query('SELECT Password, Pseudo, Email, RestorePassword FROM accounts WHERE Email = \''.DATA::getPost('email').'\'');
                $type = 'warning';
                if(mysqli_num_rows($reqAccount) > 0){
                    // if(md5(DATA::getPost('captcha')) == $_SESSION['image_random_value']){

                        $resultAccount = mysqli_fetch_object($reqAccount);
                        $filter = ['Email' => DATA::getPost('email')];
                        $data = ['RestorePassword' => 1];
                        Generique::update('accounts', 'graphene_bsm', $filter, $data);
                        $messages = 'Un email de réinitialisation du password a été envoyé sur l\'adresse ['.DATA::getPost('email').'].';
                        $type = 'success';

                        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/RestorePassword-'.base64_encode($resultAccount->Password.'::'.time().'::'.$resultAccount->Email);
                        $content = UTILS::tplMail($resultAccount->Email, $resultAccount->Pseudo, false, $url, 'passwordRecovery');
                        UTILS::MAIL($resultAccount->Email, '['.UTILS::getFunction('SiteName').'] Mot de passe perdu', $content);


                    // }else{
                    //     $messages = 'Merci de recopier correctement l\'image de verification.';
                    // }
                }else{
                    $messages = 'Désolé, aucun compte n\'est associé à l\'adresse email que vous avez saisi';
                }

                $corpMessages = '<div class="notification">';
                $corpMessages .= '<div class="alert alert-'.$type.' w-50 d-flex align-items-center justify-content-between" role="alert">';
                $corpMessages .= $messages;
                $corpMessages .= '</div>';
                $corpMessages .= '</div>';

                die(json_encode($corpMessages));
            }
        }
    }else{
        UTILS::notification('warning', 'Vous ne pouvez pas réinitialiser votre password de cette manière tout en étant connecté.', false, true);
        header('location: /Compte');
        exit;
    }

    $PAGE = $tplcompte->construire('compte');
    $TITRE = 'Mot de passe perdu ?';
    $DESCRIPTION = 'Réinitialisation de votre mot de passe';

?>
