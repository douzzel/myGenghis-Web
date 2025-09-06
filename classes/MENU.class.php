<?php
    class MENU {
        public static function menuSimple($array, $button = null){
            $tplMenu = new Template;
            $tplMenu->setFile('menu', './modules/menu/menu.html');
            $url = '#';

            foreach($array as $values){
                $tplMenu->bloc('MENU', array(
                    'NAME' => $values[0],
                    'URL' => (isset($values[1]) && $values[1] != $_SERVER['REQUEST_URI']) ? $url =  $values[1] : $url = '#',
                    'CLASS_ACTIVE' => (isset($values[1]) && $values[1] == $_SERVER['REQUEST_URI']) ? 'active' : false,
                    'DISABLED_CLICK' => (isset($values[1]) && $values[1] == $_SERVER['REQUEST_URI']) ? 'onclick="return false;"' : false
                ));
            }

            if(isset($button)){
                $tplMenu->bloc('BUTTON', array(
                    'URL' => $button[1],
                    'NAME' => $button[0],
                    'CLASS_ACTIVE' => (isset($button[1]) && $button[1] == $_SERVER['REQUEST_URI']) ? 'active' : false,
                ));
            }

            return $tplMenu->construire('menu');
        }

        public static function filArianne($array, $cat = ""){
            $tplMenu = new Template;
            $tplMenu->setFile('menu', './modules/menu/filArianne.html');
            foreach($array as $values){
                if ($cat == 'perf')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><i class="material-icons color-dark">star</i></li>'));
                else if ($cat == 'mark')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><i class="material-icons color-dark">campaign</i></li>'));
                else if ($cat == 'com')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><i class="material-icons color-dark">group</i></li>'));
                else if ($cat == 'gouv')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><i class="material-icons color-dark">business_center</i></li>'));
                else if ($cat == 'dash')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><a href="/Administration"><i class="material-icons color-dark">dashboard</i></a></li>'));
                else if ($cat == 'settings')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><a href="/Administration/Settings"><i class="material-icons color-dark">settings</i></a></li>'));
                else if ($cat == 'store')
                  $tplMenu->values(array('CAT' => '<li class="breadcrumb-item"><a href="/Administration/Settings"><i class="material-icons color-dark">store</i></a></li>'));

                $data = "";
                if (isset($values[3])) {
                  foreach ($values[3] as $key => $value) {
                    $data .= " data-{$key}='{$value}' ";
                  }
                }
                $tplMenu->bloc('MENU', array(
                    'CLASS_ACTIVE' => (isset($values[2]) && $values[2]) ? 'active' : false,
                    'ARIA_CURRENT_PAGE' =>  (isset($values[2])) ? 'aria-current="page"' : false,
                    'LINK' => (isset($values[2]) && $values[2]) ? $values[0] : '<a href="'.$values[1].'" >'.$values[0].'</a>',
                    'DATA' => $data
                ));
            }
            return $tplMenu->construire('menu');
        }
    }
?>
