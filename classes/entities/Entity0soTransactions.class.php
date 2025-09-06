<?php

class Entity0soTransactions
{
	private int $id;
	private int $company_id;
	private string $type;
	private string $paid_at;
	private float $amount;
	private string $currency_code = 'EUR';
	private float $currency_rate = 1;
	private int $account_id;
	private ?int $document_id;
	private ?int $contact_id;
	private int $category_id = 1;
	private ?string $description;
	private string $payment_method;
	private ?string $reference;
	private int $parent_id = 0;
	private ?int $created_by;
	private int $reconciled = 0;
	private ?string $created_at;
	private ?string $updated_at;
	private ?string $deleted_at;
	
}
