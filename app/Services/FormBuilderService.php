<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Forms\Form;

class FormBuilderService
{

    public function createFormSimpleBuilder($entity, $formClass, $view, $actionUrl, $redirectTo = null, $message = null, callable $afterInsert = null)
    {
        $request = request();
        dd($request);
        try {

            $form = app()->make($formClass)->createForm($entity);
            if ($request->method() == 'POST' || $request->method() == 'PUT') {
                $form->requestHandler($entity);
                $form->save();
                if ($request->ajax()) {
                    $response = [
                        'status'  => true,
                        'html'    => null,
                        'message' => $message,
                    ];
                    return response()->json($response);
                } else {
                    if ($message) {
                        Session::flash('success', $message);
                    }
                    if ($redirectTo) {
                        return redirect()->route($redirectTo);
                    }
                    if ($afterInsert) {
                        return $afterInsert($entity);
                    }
                }

            } else {
                if ($entity->isNew()) {
                    $entity->action = $actionUrl;
                    $entity->method = 'create';
                } else {
                    $entity->action = $actionUrl;
                    $entity->method = 'update';
                }
                $form = $form->buildForm($entity);
                if ($request->ajax()) {
                    $response = [
                        'status' => true,
                        'html'   => view($view, compact('entity', 'form'))->render(),
                    ];
                    return response()->json($response);
                } else {
                    return view($view, compact('entity', 'form'));
                }
            }
        } catch (\Exception$e) {
            report($e);
            if ($request->ajax()) {
                $response = [
                    'status' => false,
                    'html'   => null,
                ];
                return response()->json($response);
            }
        }
    }

}