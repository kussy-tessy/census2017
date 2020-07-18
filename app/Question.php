<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function question_group(){
        return $this->belongsTo('App\QuestionGroup');
    }
}
