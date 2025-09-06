<?php

class EntityApiToken {
  private int $id;
  private string $service;
  private string $token;
  private ?string $content;
  private ?string $name;
  private ?string $creation_date;
  private ?string $expiration_date;

  public static function updateToken($service, $token) {
    $filter = ['service' => $service];
    if ($token) {
      $data = ['token' => $token];
    } else {
      $data = ['token' => ''];
    }
    $token = Generique::select('api_token', 'graphene_bsm', $filter);
    if ($token && $token[0]) {
      Generique::update('api_token', 'graphene_bsm', $filter, $data);
    } else {
      Generique::insert('api_token', 'graphene_bsm', array_merge($filter, $data));
    }
  }
}
