<?php

namespace App\Forms;

use App\Forms\FormEntity;
use App\View\Components\Form\Email;
use App\View\Components\Form\Text;
use App\View\Components\Forms\PhoneNumber;
use App\View\Components\Forms\SingleCheckbox;
use App\View\Components\Forms\TextArea;
use App\View\Components\Forms\ZipCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

require_once app_path('Helpers/Helpers.php');

class Form
{
    
    public $fields;
    public $model;
    public $modelName;
    public $modelPath;
    public $entity;

    const TEXT         = 'text';
    const EMAIL        = 'email';
    const DROP_DOWN    = 'dropdown';
    const TEXT_AREA    = 'textarea';
    const PHONE        = 'phone';
    const ZIP_CODE     = "zipcode";
    const CHECK_BOX    = "checkbox";

    public function __construct()
    {
        $this->fields = new collection;
    }

    public function add($type, $name, $options = [])
    {
        $this->addCore($this, $type, $name, $options);

    }

    // /**
    //  * Add form
    //  */
    public function addCore($form, $type, $name, $options = [])
    {
        $field            = new FormEntity;
        $field->type      = $type;
        $field->keyName   = $name;
        $field->name      = $name;
        // $field->required  = validInput($options, 'required');
        // $field->label     = validInput($options, 'label');
        // $field->noLabel   = validInput($options, 'noLabel');
        // $field->attribute = validArray($options, 'attribute');
        // $field->class     = validInput($options, 'class');
        $field->data      = validInput($options, 'data');
        // if (!$field->noLabel) {
        //     $field->label = $this->generateLable($name);
        // }
        switch ($type) {
            case self::TEXT:
            // case self::EMAIL:
            // case self::TEXT_AREA:
            // case self::PHONE:
            // case self::ZIP_CODE:
            // case self::CHECK_BOX:

                $field->value       = validInput($options, 'value');
                // $field->placeHolder = validInput($options, 'placeHolder');
                // if (!$field->placeHolder) {
                //     $field->placeHolder = $this->generatePlaceHolder($name, $type);
                // }
                break;
            // case self::DROP_DOWN:
            //     $field->showAll = validInput($options, 'showAll');
            //     $field->entity  = validInput($options, 'entity');
            //     break;
            case 'form':
                $field->entity = validInput($options, 'entity');
                if ($field->entity) {
                    $field            = app()->make($field->entity)->createForm($field->data);
                    $modelName        = class_basename($name);
                    $field->modelName = $modelName;
                    $field->modelPath = $name;
                    $name             = $modelName;

                    foreach ($field->fields as $childField) {
                        if ($childField instanceof self) {
                            $this->subFormColumnName($childField, $modelName);
                        } else {
                            $childField->keyName = $childField->name;
                            $childField->name    = $modelName . '[' . $childField->name . ']';
                        }
                    }
                }

                break;
            default:
                break;
        }

        $form->fields->put($name, $field);
    }

    // /**
    //  * This function add the subform name in prefix to split in request
    //  */
    public function subFormColumnName($childField, $modelName)
    {
        foreach ($childField->fields as $field) {
            $field->name = $modelName . '[' . $childField->modelName . '][' . $field->keyName . ']';
        }

    }

    public function buildForm($entity)
    {
        if ($entity && $entity->id) {
            $this->model = $entity;
        }
        $this->coreBuildForm($this, $entity);
        return $this;
    }

    public function coreBuildForm($form, $entity)
    {

        foreach ($form->fields as $field) {
            if ($field instanceof self) {

                //* Add the model in edit mode /
                $childModel  = strtolower(str::snake($field->modelName));
                $childEntity = $entity->{$childModel};
                if ($childEntity && $childEntity->id) {
                    $field->model = $childEntity;
                }

                $this->coreBuildForm($field, $childEntity);
            } else {

                $fieldEntity            = new FormEntity;
                $fieldEntity->name      = $field->name;
                
                $fieldEntity->class     = $field->class;
                if (is_object($entity)) {
                    $fieldEntity->value = isset($entity->{$fieldEntity->name}) ? $entity->{$fieldEntity->name} : '';
                }

                switch ($fieldEntity->type) {
                    case self::TEXT:

                        $fieldRender = new Text($fieldEntity->name, $fieldEntity->value, $fieldEntity->class);
                        $view        = $fieldRender->render()->with($fieldRender->data());

                        if ($form->fields->has($fieldEntity->name)) {
                            $fieldObject       = $form->fields->get($fieldEntity->name);
                            $fieldObject->view = $view;
                        }

                        break;
                    case self::EMAIL:
                        // $fieldEntity->placeHolder = $field->placeHolder;

                        $fieldRender = new Email($fieldEntity->name, $fieldEntity->value , $fieldEntity->class);
                        $view        = $fieldRender->render()->with($fieldRender->data());

                        if ($form->fields->has($fieldEntity->name)) {
                            $fieldObject       = $form->fields->get($fieldEntity->name);
                            $fieldObject->view = $view;
                        }

                        break;
                    
                }
            }
        }
        // dd($this->fields);
        return $this;
    }

    public function requestHandler($entity)
    {

        $request = request()->all();
        $this->simpleRequestHandleCore($this, $entity, $request);

    }

    public function simpleRequestHandleCore($form, $entity, $request){
        foreach ($form->fields as $field) {
                $name = $field->keyName;
                switch ($field->type) {
                    case self::TEXT:
                    case self::EMAIL:
                        $entity->{$name} = Arr::get($request, $name);
                        break;
                    // case self::DROP_DOWN:
                    //     $this->dropdownDecodeValues($entity,$request,$name);
                    //     break;
                    // case self::TEXT_AREA:
                    // case self::PHONE:
                    // case self::ZIP_CODE:
                    // case self::CHECK_BOX:

                    //     $entity->{$name} = Arr::get($request, $name);
                    //     break;
                }
        }

        $form->model = $entity;
        return $entity;
    }

    public function requestHandlerCore($form, $entity, $request)
    {
        foreach ($form->fields as $field) {
            if ($field instanceof self) {

                //** Add the model in update mode */
                $childModel  = strtolower(str::snake($field->modelName));
                $childEntity = $entity->{$childModel};

                if ($entity && $entity->id) {
                    $childEntity = $entity->{$childModel};
                } else {
                    $childEntity = new $field->modelPath; //! need  to use form factory
                }

                $request = request()->all();
                $request = $this->multiKeyExists($request, $field->modelName);
                if ($request) {
                    $this->requestHandlerCore($field, $childEntity, $request);
                }

            } else {
                $name = $field->keyName;
                switch ($field->type) {
                    case self::TEXT:
                    case self::EMAIL:
                        $entity->{$name} = Arr::get($request, $name);
                        break;
                    // case self::DROP_DOWN:
                    //     $this->dropdownDecodeValues($entity,$request,$name);
                    //     break;
                    // case self::TEXT_AREA:
                    // case self::PHONE:
                    // case self::ZIP_CODE:
                    // case self::CHECK_BOX:

                    //     $entity->{$name} = Arr::get($request, $name);
                    //     break;
                }
            }
        }

        $form->model = $entity;
        return $entity;
    }

    // public function dropdownDecodeValues($entity,$request,$name){
    //     if(!is_numeric($request[$name])){
    //         $decodeValue =  decode($request[$name]);
    //         $request[$name] = $decodeValue->id;
    //     }

    //     $entity->{$name} = Arr::get($request, $name);

    //     return $entity;
    // }

    public function multiKeyExists(array $array, $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        foreach ($array as $k => $v) {
            if (!is_array($v)) {
                continue;
            }
            if (array_key_exists($key, $v)) {
                return $v[$key];
            }
        }
        return false;
    }

    public function save()
    {
        DB::beginTransaction();
        $this->saveCore($this);
        DB::commit();
    }

    public function saveCore($form, $parent = null)
    {
        if ($form->model) {
                $form->model->save();
            }

        foreach ($form->fields as $field) {
            if ($field instanceof self) {
                $this->saveCore($field, $form);
            }

        }

    }

    public function getFormName($name)
    {
        $result = ['formName' => "", "colName" => ""];
        preg_match('/^(.*?)\.(.*)$/', $name, $matches);
        if (count($matches) > 0 && $matches[1] && $matches[2]) {
            $result['formName'] = $matches[1];
            $result['colName']  = $matches[2];
            return $result;
        }

        // $name = explode('.', $name);
        return $name;
    }

    public function text($name)
    {
        return $this->textCore($this, $name);
    }
    public function textCore($form, $name, $s = 0)
    {

        if (str::contains($name, '.')) {
            $result = $this->getFormName($name);
            if ($form->fields->has($result['formName'])) {
                $fields = $form->fields->get($result['formName']);
                return $this->textCore($fields, $result['colName'], 1);
            }

        } else {

            if ($form->fields->has($name)) {
                return $form->fields->get($name)->view;
            }
        }

        return null;
    }

    public function phone($name)
    {
        return $this->text($name);
    }

    public function dropDown($name)
    {

        return $this->text($name);
    }

    public function textarea($name)
    {
        return $this->text($name);
    }

    // public function generateLable($name)
    // {
    //     $name   = Lang::get("messages.$name");
    //     $name   = str::ucfirst($name);
    //     $newstr = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $name);
    //     return $newstr;
    // }

    // public function generatePlaceHolder($name, $type)
    // {
    //     $label       = $this->generateLable($name);
    //     $placeHolder = '';
    //     switch ($type) {
    //         case 'text':
    //         case 'name':
    //         case 'email':
    //             $placeHolder = Lang::get('messages.enter') . ' ' . $label;
    //             break;
    //     }
    //     return $placeHolder;
    // }
}