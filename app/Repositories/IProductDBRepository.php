<?php


namespace App\Repositories;


interface IProductDBRepository
{
    public function get(string $filter):array;

}