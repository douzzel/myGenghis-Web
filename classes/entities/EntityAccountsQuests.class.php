<?php

class EntityAccountsQuests
{
    private int $id_client;
    private ?int $quest_documents;
    private ?int $quest_contacts;
    private ?int $quest_membres;

    private ?int $quest_devis;
    private ?int $quest_facture_status;
    private ?int $quest_click_and_collect;
    private ?int $quest_reservations;

    private ?int $quest_product;
    private ?int $quest_family;
    private ?int $quest_category;
    private ?int $quest_vente_settings;

    private ?int $quest_web;
    private ?int $quest_article;
    private ?int $quest_live_chat;

    private ?int $hide_menu;

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getQuestProduct()
    {
        return $this->quest_product;
    }

    public function getQuestFamily()
    {
        return $this->quest_family;
    }

    public function getQuestCategory()
    {
        return $this->quest_category;
    }

    public function getQuestDevis()
    {
        return $this->quest_devis;
    }

    public function getQuestFactureStatus()
    {
        return $this->quest_facture_status;
    }

    public function getQuestDocuments()
    {
        return $this->quest_documents;
    }

    public function getQuestContacts()
    {
        return $this->quest_contacts;
    }

    public function getQuestMembres()
    {
        return $this->quest_membres;
    }

    public function getHideMenu()
    {
        return $this->hide_menu;
    }

    public function getQuestVenteSettings()
    {
        return $this->quest_vente_settings;
    }

    public function getQuestClickAndCollect()
    {
        return $this->quest_click_and_collect;
    }

    public function getQuestReservations()
    {
        return $this->quest_reservations;
    }

    public function getQuestWeb()
    {
        return $this->quest_web;
    }

    public function getQuestArticle()
    {
        return $this->quest_article;
    }

    public function getQuestLiveChat()
    {
        return $this->quest_live_chat;
    }

    public function countQuestVente() {
        return ($this->getQuestProduct() + $this->getQuestFamily() + $this->getQuestCategory() + $this->getQuestVenteSettings());
    }

    public function countQuestPerf() {
        return ($this->getQuestDevis() + $this->getQuestFactureStatus() + $this->getQuestClickAndCollect() + $this->getQuestReservations());
    }

    public function countQuestGouv() {
        return ($this->getQuestDocuments() + $this->getQuestContacts() + $this->getQuestMembres());
    }

    public function countQuestComm() {
        return ($this->getQuestWeb() + $this->getQuestArticle() + $this->getQuestLiveChat());
    }

    private function round_down($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }

    public function calculateLevel() {
        $number_quests = $this->quest_documents + $this->quest_contacts + $this->quest_membres + $this->quest_devis + $this->quest_facture_status + $this->quest_click_and_collect + $this->quest_reservations + $this->quest_product + $this->quest_family + $this->quest_category + $this->quest_vente_settings + $this->quest_web + $this->quest_article + $this->quest_live_chat;
        return $this->round_down($number_quests / 8, 0) + 1;
    }

    public function calculateLevelPercent() {
        $number_quests = $this->quest_documents + $this->quest_contacts + $this->quest_membres + $this->quest_devis + $this->quest_facture_status + $this->quest_click_and_collect + $this->quest_reservations + $this->quest_product + $this->quest_family + $this->quest_category + $this->quest_vente_settings + $this->quest_web + $this->quest_article + $this->quest_live_chat;
        return 100 * ($number_quests % 8) / 8;
    }
}
