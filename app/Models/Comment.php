<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = array('post_id', 'author_name', 'comment_body');

    public $timestamps = true;
}