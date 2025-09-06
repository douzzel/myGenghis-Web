<?php

class ManageProductColumns
{
    public static function load() {
        if (DATA::getMethod() == 'POST') {
            self::addColumn();
            self::updateColumn();
            self::deleteColumn();
            self::deleteColumnOK();
            self::columnsOrder();
        }
    }

    public static function addColumn() {
        if (DATA::isPost('addProductColumn')) {
            $name = DATA::getPost('addProductColumnName');
            $type = DATA::getPost('addProductColumnType');
            $hidden = DATA::getPost('addProductHiddenStore');

            $filter = ['name' => $name];
            $verifExist = Generique::selectOne('product_columns_settings', 'graphene_bsm', $filter);
            if ($verifExist) {
                UTILS::notification('danger', 'Ce champ existe déjà');
            }

            $maxSort = MYSQL::selectOneValue('SELECT max(sort_order) FROM product_columns_settings');
            $sort_order = isset($maxSort[0]) ? ($maxSort[0] + 1) : 1;
            $data = [
                'name' => $name,
                'type' => $type,
                'sort_order' => $sort_order,
                'hidden_store' => $hidden];
            Generique::insert('product_columns_settings', 'graphene_bsm', $data);
            $id = Generique::selectMaxId('product_columns_settings', 'graphene_bsm');
            if ($type == 'select') {
                if (DATA::getPost('addProductColumnSelect')) {
                    $options = preg_split('/,/', DATA::getPost('addProductColumnSelect'));
                    foreach ($options as $option) {
                        $data = ['id_product_columns_settings' => $id, 'value' => $option];
                        Generique::insert('product_columns_select', 'graphene_bsm', $data);
                    }
                }
                $type = 'text';
            }
            MYSQL::query("ALTER TABLE `product_columns` ADD `{$id}` {$type} NULL DEFAULT NULL");

            UTILS::notification('success', "Champ {$name} ajouté avec succès");
        }
    }

    public static function deleteColumn() {
        if (DATA::isPost('deleteProductColumn')) {
            $id = DATA::getPost('deleteProductColumn');
            $filter = ['id' => $id];
            $col = Generique::selectOne('product_columns_settings', 'graphene_bsm', $filter);
            UTILS::Alert('danger', "Voulez-vous vraiment supprimer le champ {$col->getName()} ?", 'L\'action sera irréversible', $_SERVER['REQUEST_URI'], 'deleteProductColumnOK', DATA::getPost('deleteProductColumn'));
        }
    }

    public static function deleteColumnOK() {
        if (DATA::isPost('deleteProductColumnOK')) {
            $id = DATA::getPost('deleteProductColumnOK');

            $filter = ['id' => $id];
            $verifExist = Generique::selectOne('product_columns_settings', 'graphene_bsm', $filter);
            if ($verifExist) {
                MYSQL::query("ALTER TABLE `product_columns` DROP `{$id}`");
                Generique::delete('product_columns_settings', 'graphene_bsm', $filter);
                UTILS::notification('success', "Champ {$verifExist->getName()} supprimé avec succès");
            } else {
                UTILS::notification('danger', "Ce champ n'existe pas");
            }
        }
    }

    public static function updateColumn() {
        if (DATA::isPost('updateProductColumn')) {
            $id = DATA::getPost('updateProductColumn');
            $name = DATA::getPost('updateProductColumnName');

            $filter = ['id' => $id];
            $data = ['name' => $name];

            Generique::update('product_columns_settings', 'graphene_bsm', $filter, $data);
        }
    }

    private static function createInput($id, $name, $type, $value = '', $hiddenStore = 0) {
        $css = $hiddenStore ? 'font-italic' : '';
        if ($type == 'text') {
            return "<div class='col-sm-6 col-lg-4 mb-3'>
                <label for='customColumn{$id}' class='{$css}'>{$name}</label>
                <input data-role='input' type='text' name='customColumn{$id}' placeholder='{$name}' value='{$value}'>
            </div>";
        } else if ($type == 'date') {
            return "<div class='col-sm-6 col-lg-4 mb-3'>
                <label for='customColumn{$id}' class='{$css}'>{$name}</label>
                <input data-role='input' type='date' name='customColumn{$id}' placeholder='{$name}' value='{$value}'>
            </div>";
        } else if ($type == 'int') {
            return "<div class='col-sm-6 col-lg-4 mb-3'>
                <label for='customColumn{$id}' class='{$css}'>{$name}</label>
                <input data-role='input' type='number' name='customColumn{$id}' placeholder='{$name}' value='{$value}'>
            </div>";
        } else if ($type == 'select') {
            $filter = ['id_product_columns_settings' => $id];
            $options = Generique::select('product_columns_select', 'graphene_bsm', $filter);
            if ($options) {
                $html = "<div class='col-sm-6 col-lg-4 mb-3'>
                        <label for='customColumn{$id}' class='{$css}'>{$name}</label>
                        <select data-role='select' name='customColumn{$id}' data-add-empty-value='true' data-filter='false'>";
                foreach ($options as $option) {
                    $selected = $option->getValue() == $value ? 'selected' : '';
                    $html .= "<option value='{$option->getValue()}' {$selected}>{$option->getValue()}</option>";
                }
                $html .= "
                </select>
                </div>";
                return $html;

            } else {
                return "<div class='col-sm-6 col-lg-4 mb-3'>
                    <label for='customColumn{$id}' class='{$css}'>{$name}</label>
                    <input data-role='input' type='text' name='customColumn{$id}' placeholder='{$name}' value='{$value}'>
                </div>";
            }
        }
    }

    public static function getHtmlProduct($productId = false) {
        $filter = [];
        $columns = Generique::select('product_columns_settings', 'graphene_bsm', $filter, 'sort_order ASC');
        $html = '';
        $values = [];

        if ($productId) {
            $values = MYSQL::selectOneRow("SELECT * FROM product_columns WHERE product_id = '{$productId}'");
        }
        foreach ($columns as $col) {
            $value = $productId && $values && $values[$col->getId()] ? $values[$col->getId()] : '';
            $html .= self::createInput($col->getId(), $col->getName(), $col->getType(), $value, $col->getHiddenStore());
        }
        return $html;
    }


    private static function createLineStore($name, $type, $value) {
        if ($value) {
            if ($type == 'date')
                $value = UTILS::date($value, 'd/m/Y');
            return "<b>{$name} :</b> {$value}<br/>";
        }
        return '';
    }

    public static function getHtmlStore($productId) {
        $filter = ['hidden_store' => 0];
        $columns = Generique::select('product_columns_settings', 'graphene_bsm', $filter, 'sort_order ASC');
        $html = '';
        $values = [];

        if ($productId) {
            $values = MYSQL::selectOneRow("SELECT * FROM product_columns WHERE product_id = '{$productId}'");
        }
        foreach ($columns as $col) {
            $value = $values && $values[$col->getId()] ? $values[$col->getId()] : '';
            $html .= self::createLineStore($col->getName(), $col->getType(), $value);
        }
        return $html;
    }

    public static function columnsOrder() {
        if (DATA::isPost('columnsOrder')) {
            $columnList = $_POST['columnsOrder'];
            for ($i=0; $i < count($columnList) ; $i++) { 
                $filter = ['id' => $columnList[$i]];
                $data = ['sort_order' => $i];
                Generique::update('product_columns_settings', 'graphene_bsm', $filter, $data);
            }
            exit;
        }
    }
}
