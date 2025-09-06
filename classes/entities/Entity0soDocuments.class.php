<?php

class Entity0soDocuments
{
	private int $id;
	private int $company_id;
	private string $type;
	private string $document_number;
	private ?string $order_number;
	private string $status;
	private string $issued_at;
	private string $due_at;
	private float $amount;
	private string $currency_code;
	private float $currency_rate;
	private int $category_id;
	private int $contact_id = 1;
	private string $contact_name;
	private ?string $contact_email;
	private ?string $contact_tax_number;
	private ?string $contact_phone;
	private ?string $contact_address;
	private ?string $notes;
	private ?string $footer;
	private int $parent_id = 0;
	private ?int $created_by;
	private ?int $created_at;
	private ?int $updated_at;
	private ?int $deleted_at;
}
