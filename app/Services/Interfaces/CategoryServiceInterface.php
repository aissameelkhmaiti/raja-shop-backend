<?php
namespace App\Services\Interfaces;

interface CategoryServiceInterface
{
    public function getAll();
    public function store(array $data);
    public function update($id, array $data);
    public function destroy($id);
}

