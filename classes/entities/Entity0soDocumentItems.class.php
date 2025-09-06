<?php

class Entity0soDocumentItems
{
	private int $id;
	private int $company_id;
	private string $type;
	private string $document_id;
	private int $item_id;
	private string $name;
	private ?string $description;
	private ?string $sku;
	private float $quantity;
	private float $price;
	private float $tax = 0;
	private string $discount_type;
	private float $discount_rate = 0;
	private float $total;
	private ?int $created_at;
	private ?int $updated_at;
	private ?int $deleted_at;
}
