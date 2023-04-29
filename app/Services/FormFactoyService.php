<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Forms\Form;

class FormFactoryService
{
    public function createUser(User $user){

        if($user){
            $user = $user;
        }else{
            $user = new User();
        }
        
        return $user;
    }
}