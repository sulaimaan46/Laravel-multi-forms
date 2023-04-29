<?php

use Illuminate\Support\Facades\Validator;

    if (! function_exists('validInput')) {
        function validInput($inputValue, $rules) {
            $validator = Validator::make(['input' => $inputValue], ['input' => $rules]);
            return $validator;
        }
    }
