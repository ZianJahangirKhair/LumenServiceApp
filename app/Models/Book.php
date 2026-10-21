<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = array(
        'title', 'author', 'isbn', 'publisher',
        'published_year', 'price', 'stock', 'status'
    );

    public $timestamps = true;
}