<?php

namespace App\Http\Controllers;

use App\Forms\UserForm;
use App\Models\User;
use App\Services\FormBuilderService;
use App\Services\FormFactoryService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $formFactoryService;
    public $formBuilderService;

    public function __construct(FormBuilderService $formBuilderService , FormFactoryService $formFactoryService)
    {
        $this->formBuilderService = $formBuilderService;
        $this->formFactoryService = $formFactoryService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $entity = $this->formFactoryService->createUser($user);
        return $this->process($entity, $user);
    }
    public function edit(user $user)
    {

        $entity = $this->formFactoryService->createUser($user);
        return $this->process($entity, $user);
    }

    public function store(user $user)
    {
        $entity = $this->formFactoryService->createUser($user);
        return $this->process($entity, $user);
    }

    public function update(user $user)
    {
        $entity = $this->formFactoryService->createUser($user);
        return $this->process($entity, $user);
    }

    public function destroy(user $user)
    {
        $user->delete();
        dd("deleted");
    }

    public function process(User $entity, user $user)
    {
        if ($entity->isNew()) {
            $actionUrl = route('users.store', ['user' => $user]);
            $message   = "User Added";
        } else {
            $actionUrl = route('users.update', ['user' => $user]);
            $message   = "User Updated";
        }

        return $this->formBuilderService->createFormSimpleBuilder($entity, UserForm::class, 'users/form', $actionUrl, null, $message,null );
    }
}
