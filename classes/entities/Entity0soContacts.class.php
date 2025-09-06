<?php

class Entity0soContacts
{
	private int $id;
	private int $company_id;
	private string $type;
	private string $name;
	private ?string $email;
	private ?int $user_id;
	private ?string $tax_number;
	private ?string $phone;
	private ?string $address;
	private ?string $website;
	private ?string $currency_code = 'EUR';
	private int $enabled = 1;
	private ?string $reference;
	private ?int $created_by;
	private ?string $created_at;
	private ?string $updated_at;
	private ?string $deleted_at;
	
	public function getId()
	{
		return $this->id;
	}
}
