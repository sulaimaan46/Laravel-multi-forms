<?php

namespace App\Forms;

use App\Forms\Form;

class UserForm extends Form
{

    public function createForm($entity)
    {
        // $payeeNameattr =[
        //     "data-parsley-payeenameexists",
        //     "data-parsley-required-message ='".__('messages.payee_name_is_required')."'",
        //     "data-url =" . route('client-payees.checkPayeeName',['client' => encode($entity->client_id,Client::class)]),
        // ];
        // $emailAttr    = ["data-parsley-required-message='".__('messages.email_is_required')."'"];

        // if($entity->id){
        //     array_push($payeeNameattr,"data-entity-id = ". $entity->id);
        // }
        $this->add(self::TEXT,'name');

        // $this->add(self::EMAIL, 'email', ['required' => true]);
        return $this;
    }
}