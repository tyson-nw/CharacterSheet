<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    use HasFactory;

    public $data;
    public function __construct(){
        $this->data = json_decode(file_get_contents( database_path() . "/sources/Features.json"), true);
        
    }
    public function has($value){
        return isset($this->data[$value]);
    }
    
}
