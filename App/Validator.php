<?php

namespace App;

use Exception;

class Validator 
{
    private static array $errors = [];
    public static function check (array $fields, array $rules): array  
    {
        foreach ($rules as $fieldName => $fieldRulesString) {
            $fieldRules = explode('|',$fieldRulesString);
            $fieldValue = $fields[$fieldName];
            foreach ($fieldRules as $rule) {
                if($rule == 'required') 
                    { self::required($fieldName,$fieldValue); continue;}

                if(str_contains($rule,'max:')) 
                    { self::max($fieldName,$fieldValue,$rule); continue;}

                if($rule == 'email') 
                    { self::email($fieldName,$fieldValue,$rule); continue;}

                if($rule == 'password') 
                    { self::password($fieldName,$fieldValue); continue;}

                if($rule == 'confirm') 
                    { self::confirm($fieldName,$fieldValue,$fields); continue;}
            }
        }

        if(!self::$errors) return $fields;

        $res = Response::json([
            'message' => 'Validation Faild',
            'errors' => self::$errors
        ],422);

        exit($res);
    }

    private static function required (string $fieldName,$fieldValue) 
    {
        if(!empty($fieldValue)) return;
        $errMessage = "$fieldName is required";
        self::AddError($fieldName,$errMessage);
    }

    private static function max (string $fieldName,$fieldValue, string $rule) 
    {
        $max = str_replace('max:','',$rule);

        if(\strlen($fieldValue) <= $max) return;
        $errMessage = "$fieldName max length is $max";
        self::AddError($fieldName,$errMessage);
    }

    private static function min (string $fieldName,$fieldValue, string $rule) 
    {
        $min = str_replace('min:','',$rule);

        if(\strlen($fieldValue) >= $min) return;
        $errMessage = "$fieldName min length is $min";
        self::AddError($fieldName,$errMessage);
    }

    private static function email (string $fieldName,$fieldValue, string $rule) 
    {
        if(filter_var($fieldValue,FILTER_VALIDATE_EMAIL)) return;
        $errMessage = "$fieldName  is Not a Valid Email";
        self::AddError($fieldName,$errMessage);
    }

    private static function password (string $fieldName,$fieldValue) 
    {
        self::min($fieldName,$fieldValue,'min:8');
        self::hasCapitalLetter($fieldName,$fieldValue);
        self::hasSmallLetter($fieldName,$fieldValue);
        self::hasSymbol($fieldName,$fieldValue);
        self::int($fieldName,$fieldValue);
    }

    private static function hasCapitalLetter (string $fieldName,$fieldValue) 
    {
        for ($i=0; $i >= 0; $i++) { 
            if(!isset($fieldValue[$i])) break;
            $letter = $fieldValue[$i];
            if(ctype_upper($letter)) return;
        }
        $errMessage = "$fieldName Must Have at Least One Captial Letter";
        self::AddError($fieldName,$errMessage);
    }

    private static function hasSmallLetter (string $fieldName,$fieldValue) 
    {
        for ($i=0; $i >= 0; $i++) { 
            if(!isset($fieldValue[$i])) break;
            $letter = $fieldValue[$i];
            if(ctype_lower($letter)) return;
        }
        $errMessage = "$fieldName Must Have at Least One Small Letter";
        self::AddError($fieldName,$errMessage);
    }

    private static function hasSymbol (string $fieldName,$fieldValue) 
    {
        for ($i=0; $i >= 0; $i++) { 
            if(!isset($fieldValue[$i])) break;
            $letter = $fieldValue[$i];
            if(!ctype_alnum($letter)) return;
        }
        $errMessage = "$fieldName Must Have at Least One Symbol";
        self::AddError($fieldName,$errMessage);
    }

    private static function int (string $fieldName,$fieldValue) 
    {
        for ($i=0; $i >= 0; $i++) { 
            if(!isset($fieldValue[$i])) break;
            $letter = $fieldValue[$i];
            if(ctype_digit($letter)) return;
        }
        $errMessage = "$fieldName Must Have at Least One Number";
        self::AddError($fieldName,$errMessage);
    }

    private static function confirm (string $fieldName,$fieldValue, array $fields) 
    {
        if(isset($fields[$fieldName."_confirm"]) && $fields[$fieldName."_confirm"] == $fieldValue) return;
        $errMessage = "$fieldName Must Match $fieldName"."_confirm";
        self::AddError($fieldName,$errMessage);
    }

    private static function AddError (string $fieldName,string $errMessage) 
    {
        if(isset(self::$errors[$fieldName]))
            self::$errors[$fieldName][] = $errMessage;
        else self::$errors[$fieldName] = [$errMessage];
    }
}