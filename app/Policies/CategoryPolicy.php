<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Kategoriyi görüntüleyebilir mi?
     */
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
    /**
     * Kategoriyi güncelleyebilir mi?
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Kategoriyi silebilir mi?
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
}
