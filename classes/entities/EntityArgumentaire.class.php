<?php

class EntityArgumentaire {
	private int $id;
	private string $type;
	private string $date;
	private int $id_employe;
	private string $titre;

	public function getId()
	{
		return $this->id;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function getIdEmploye()
	{
		return $this->id_employe;
	}

	public function getTitre()
	{
		return $this->titre;
	}
}
