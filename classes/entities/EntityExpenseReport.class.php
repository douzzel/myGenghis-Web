<?php

class EntityExpenseReport
{
    private int $id;
	private int $id_client;
	private int $number;
	private ?string $date;
	private ?string $name;
	private ?float $total_price;
	private ?float $km_number;
	private ?float $km_price;
	private ?float $food_price;
	private ?float $accommodation_price;
	private ?float $other_price;
	private ?float $tva;
	private ?string $reconciliation_date;
	private ?string $reimbursement_date;
	private ?string $path_file;

	public function getNumber()
	{
		return $this->number;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getTotalPrice()
	{
		return $this->total_price;
	}

	public function getKmNumber()
	{
		return $this->km_number;
	}

	public function getKmPrice()
	{
		return $this->km_price;
	}

	public function getFoodPrice()
	{
		return $this->food_price;
	}

	public function getAccommodationPrice()
	{
		return $this->accommodation_price;
	}

	public function getOtherPrice()
	{
		return $this->other_price;
	}

    public function getId()
    {
        return $this->id;
    }

	public function getReimbursementDate()
	{
		return $this->reimbursement_date;
	}

	public function getTva()
	{
		return $this->tva;
	}

	public function getReconciliationDate()
	{
		return $this->reconciliation_date;
	}

	public function getPathFile()
	{
		return $this->path_file;
	}
}
