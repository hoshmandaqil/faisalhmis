<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expenses_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * subCategories: Relation to self class for retrieving subcategories
     *
     * @return object
     */
    public function subCategories()
    {
        return $this->hasMany($this, 'parent', 'id');
    }

    /**
     * parentCategory: Parent Category
     *
     * @return object
     */
    public function parentCategory()
    {
        return $this->belongsTo($this, 'parent', 'id');
    }
}
