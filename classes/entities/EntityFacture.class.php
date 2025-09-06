<?php

class EntityFacture
{
    private int $id;
    private int $FID;
    private ?int $ref_FID;
    private ?string $ref_avenant;
    private ?string $email;
    private ?string $article;
    private ?string $description;
    private ?float $price;
    private ?string $product_discount;
    private ?float $total_cart;
    private float $tva_percent;
    private float $tva_total;
    private ?float $delivery;
    private ?float $total_order;
    private ?string $addresse;
    private ?string $address_complement;
    private string $cp;
    private string $ville;
    private string $pays;
    private string $nom;
    private string $prenom;
    private ?string $civilite;
    private ?string $phone;
    private ?float $quantities;
    private ?float $v_steres;
    private ?float $v_ranges;
    private ?int $statut;
    private ?string $date;
    private ?int $type;
    private ?float $discount_pct;
    private ?float $discount_val;
    private ?string $image;
    private ?string $imageProduct;
    private ?string $imageWidth;
    private ?string $video;
    private ?int $sign;
    private ?float $acompte_val;
    private ?string $text1;
    private ?string $text2;
    private ?string $moyenPaiement;
    private ?int $acquitee;
    private ?string $date_acquitee;
    private ?int $id_tier;
    private ?int $id_contact;
    private ?string $numero_facture;
    private ?string $p_family;
    private ?string $offerValidity;
    private ?int $no_fact_ant;
    private ?string $modified;
    private ?int $subscription_interval_count;
    private ?string $subscription_interval;
    public ?string $money;

    public function __toString() {
        return "{$this->id}";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNumero_facture()
    {
        return $this->numero_facture;
    }

    public function getFID()
    {
        return $this->FID;
    }

    public function getNumeroFactureOrFID() {
        if ($this->numero_facture)
            return $this->numero_facture;
        return $this->FID;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getArticle()
    {
        return $this->article;
    }

    public function getQuantities()
    {
        return $this->quantities;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getTvaPercent()
    {
        return $this->tva_percent;
    }

    public function getProductDiscount()
    {
        return $this->product_discount;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTextStatut() {
        switch ($this->statut) {
            case 0:
                return 'Devis Brouillon';
            case 1:
                return 'Devis';
            case 2:
                return 'Bon de livraison';
            case 3:
                return 'Facture en cours';
            case 4:
                return 'Facture';
            case 5:
                return 'Facture Brouillon';
            case 30:
                return 'Devis validé';
            case 31:
                return 'Devis perdu';
            case 30:
                return 'Avenant';
        }
    }

    // Return Devis, BonLivraison or Facture
    public function getTextNameStatut() {
        if ($this->statut == 0 || $this->statut == 1);
            return 'Devis';
        if ($this->statut == 2)
            return 'BonLivraison';
        return 'Facture';
    }

    public function getAcquitee()
    {
        return $this->acquitee;
    }

    public function getTvaTotal()
    {
        return $this->tva_total;
    }

    public function getTotal_order()
    {
        return $this->total_order;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDate_acquitee()
    {
        return $this->date_acquitee;
    }

    public function getMoyenPaiement()
    {
        return $this->moyenPaiement;
    }

    public function getIdTier()
    {
        return $this->id_tier;
    }

    public function getIdContact()
    {
        return $this->id_contact;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAcompteVal()
    {
        return $this->acompte_val;
    }

    private function getContactAkaunting() {
        $contact = null;
        if ($this->id_tier) {
            $filter = ['reference' => "TI-{$this->id_contact}"];
            $contact = Generique::selectOne('0so_contacts', 'graphene_akaunting', $filter);
        }
        if ($this->email) {
            $filter = ['Email' => $this->email];
            $member = Generique::selectOne('accounts', 'graphene_bsm', $filter);
            if ($member) {
                $filter = ['reference' => "AC-{$member->getIdClient()}"];
                $contact = Generique::selectOne('0so_contacts', 'graphene_akaunting', $filter);
            }
        }
        if (!$contact && $this->id_contact) {
            $filter = ['reference' => "CO-{$this->id_contact}"];
            $contact = Generique::selectOne('0so_contacts', 'graphene_akaunting', $filter);
        }
        return $contact;
    }

    private function toAkaunting()
    {
        $contact = $this->getContactAkaunting();
        return ['id' => $this->FID, 'company_id' => 1, 'type' => 'invoice', 'document_number' => $this->numero_facture ? $this->numero_facture : $this->FID, 'status' => 'sent', 'issued_at' => $this->date, 'due_at' => $this->date_acquitee ?? $this->modified, 'amount' => $this->total_order, 'currency_code' => 'EUR', 'currency_rate' => 1, 'category_id' => 2, 'contact_id' => $contact ? $contact->getId() : 0, 'contact_name' => "{$this->nom} {$this->prenom}", 'contact_email' => $this->email, 'contact_address' => "{$this->addresse} {$this->ville} {$this->cp} {$this->pays}", 'notes' => $this->text2, 'footer' => $this->text1, 'created_at' => $this->date];
    }

    private function saveProductsToAkaunting() {
        $filter = ['FID' => $this->FID];
        $factures = Generique::select('facture', 'graphene_bsm', $filter);
        foreach ($factures as $f) {
            $filter = ['name' => $f->getArticle()];
            $product = Generique::selectOne('products', 'graphene_bsm', $filter);

            $product_discount = $f->getProductDiscount();
            $product_discount_number = number_format((float)$f->getProductDiscount(), 2, '.', '');
            $totalPrice = strpos($product_discount, '%') ? $f->getQuantities() * ($f->getPrice() - ($f->getPrice() * $product_discount_number / 100)) : $f->getQuantities() * ($f->getPrice() - $product_discount_number);

            $data = ['company_id' => 1, 'type' => 'invoice', 'document_id' => $this->FID, 'item_id' => $product ? $product->getId() : 0, 'name' => html_entity_decode($f->getArticle()), 'description' => html_entity_decode($f->getDescription()), 'quantity' => $f->getQuantities(), 'price' => $f->getPrice(), 'tax' => $f->getTvaPercent(), 'discount_type' => 'normal', 'discount_rate' => 0, 'total' => $totalPrice, 'created_at' => $f->getDate()];
            Generique::insert('0so_document_items', 'graphene_akaunting', $data);
        }
    }

    public function saveToAkaunting() {
        $data = $this->toAkaunting();
        if (!Generique::selectOne('0so_documents', 'graphene_akaunting', ['id' => $this->FID])) {
            Generique::insert('0so_documents', 'graphene_akaunting', $data);
            $data = ['company_id' => 1, 'type' => 'invoice', 'document_id' => $this->FID, 'code' => 'sub_total', 'name' => 'invoices.sub_total', 'amount' => $this->total_cart, 'sort_order' => 1, 'created_at' => $this->date];
            Generique::insert('0so_document_totals', 'graphene_akaunting', $data);
            $data['code'] = 'tax';
            $data['name'] = 'TVA';
            $data['amount'] = $this->tva_total;
            $data['sort_order'] = 2;
            Generique::insert('0so_document_totals', 'graphene_akaunting', $data);
            $data['code'] = 'total';
            $data['name'] = 'invoices.total';
            $data['amount'] = $this->total_order;
            $data['sort_order'] = 3;
            Generique::insert('0so_document_totals', 'graphene_akaunting', $data);
            $this->saveProductsToAkaunting();
        }
        $this->updatePaidAkaunting();
    }

    public function updatePaidAkaunting() {
        $filter = ['document_id' => $this->FID];
        if (!$this->acquitee) {
            Generique::delete('0so_transactions', 'graphene_akaunting', $filter);
            $data = ['status' => 'sent'];
            $filter = ['id' => $this->FID];
            Generique::update('0so_documents', 'graphene_akaunting', $filter, $data);
        } else {
            $transactions = Generique::selectOne('0so_transactions', 'graphene_akaunting', $filter);
            if (!$transactions) {
                $contact = $this->getContactAkaunting();
                // TODO add payment method to offline-payments in settings
                $data = ['company_id' => 1, 'type' => 'income', 'paid_at' => $this->date_acquitee ?? $this->modified, 'amount' => $this->total_order, 'currency_code' => 'EUR', 'currency_rate' => 1, 'account_id' => 1, 'document_id' => $this->FID, 'category_id' => 3, 'contact_id' => $contact ? $contact->getId() : 0, 'payment_method' => 'offline-payments.cash.1', 'created_at' => $this->date_acquitee ?? $this->modified, 'description' => html_entity_decode("{$this->moyenPaiement} - Facture {$this->numero_facture}")];
                Generique::insert('0so_transactions', 'graphene_akaunting', $data);
                $data = ['status' => 'paid'];
                $filter = ['id' => $this->FID];
                Generique::update('0so_documents', 'graphene_akaunting', $filter, $data);
            } else {
                $data = ['description' => html_entity_decode("{$this->moyenPaiement} - Facture {$this->numero_facture}")];
                Generique::update('0so_transactions', 'graphene_akaunting', $filter, $data);
            }
        }
    }
}
