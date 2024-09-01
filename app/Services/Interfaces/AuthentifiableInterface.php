<?php


namespace App\Services\Interfaces;


interface AuthentifiableInterface{
    public function regiser(array $data): array;
    public function login(array $data): array;
}