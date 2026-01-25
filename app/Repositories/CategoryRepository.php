<?php

namespace App\Repositories;

use App\Models\Categorie;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Categorie::all();
    }

    public function create(array $data)
    {
        return Categorie::create($data);
    }

    public function find($id)
    {
        return Categorie::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category; // Retourner l'objet mis à jour
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
