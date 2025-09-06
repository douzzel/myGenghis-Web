<?php

class EntityClickAndCollect
{
    private int $id;
    private int $FID;
    private int $accounts_id;
    private int $status;
    private ?string $comment;
    private string $creation_date;
    private ?string $delivery_date;
    private ?string $package_expedition_date;
    private ?string $product_expedition_date;

    public function getFID()
    {
        return $this->FID;
    }

    public function getNumeroFacture()
    {
        $filter = ['FID' => $this->FID];
        return Generique::selectOne('facture', 'graphene_bsm', $filter)->getNumeroFactureOrFID();
    }

    public function getAccountsId()
    {
        return $this->accounts_id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }

    public function getCreationDateText()
    {
        return UTILS::date($this->creation_date);
    }

    public function getStatusText() {
        switch ($this->status) {
            case CLICK_WAIT_PAYMENT:
                return '<span>En attente de réglement</span>';
            case CLICK_TO_PREPARE:
                return '<span class="color-cyan">À préparer</span>';
            case CLICK_PREPARATION:
                return '<span class="color-cyan">En préparation</span>';
            case CLICK_SENT:
                return '<span class="color-warning">Expédié</span>';
            case CLICK_AVAILABLE:
                return '<span class="color-success">Retrait disponible</span>';
            case CLICK_RETRIEVED:
                return '<span class="color-success">Réceptionné</span>';
            case CLICK_ERROR_STOCK:
                return '<span class="color-danger">Erreur stock</span>';
            case CLICK_ERROR_PAY:
                return '<span class="color-danger">Erreur paiement</span>';
            case CLICK_ERROR_PRODUCT:
                return '<span class="color-danger">Erreur aiguillage</span>';
            case CLICK_CANCEL:
                return '<span class="color-danger">Annulé</span>';
            case CLICK_CANCEL_AFTER_EXP:
                return '<span class="color-danger">Annulé après expédition</span>';
            case CLICK_CANCEL_NOT_DELIVERED:
                return '<span class="color-danger">Non livré</span>';
            case CLICK_RETURN_MANUF_WARANTLY:
                return '<span class="color-danger">Retour sous garantie constructeur</span>';
            case CLICK_RETURN_PROVID_WARANTLY:
                return '<span class="color-danger">Retour sous garantie fournisseur</span>';
            case CLICK_RETURN_NO_WARANTLY:
                return '<span class="color-danger">Retour sans garantie</span>';
            default:
                return '';
        }
    }
}
