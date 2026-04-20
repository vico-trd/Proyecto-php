<?php

namespace App\Repositories;

interface RepositoryInterface {

    public function findById(int $id): ?object;

    public function findAll():array;


    public function save(object $object):bool;

    public function delete(int $id):bool;







}