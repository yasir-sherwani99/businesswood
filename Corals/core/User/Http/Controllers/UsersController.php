<?php

namespace Corals\User\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\User\DataTables\UsersDataTable;
use Corals\User\Http\Requests\UserRequest;
use Corals\User\Models\User;
use Corals\User\Services\UserService;
use Illuminate\Http\Request;

class UsersController extends BaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->resource_url = config('user.models.user.resource_url');

        $this->title = 'User::module.user.title';
        $this->title_singular = 'User::module.user.title_singular';

        parent::__construct();
    }

    /**
     * @param UserRequest $request
     * @param UsersDataTable $dataTable
     * @return mixed
     */
    public function index(UserRequest $request, UsersDataTable $dataTable)
    {
        return $dataTable->render('User::users.index');
    }

    /**
     * @param UserRequest $request
     * @return $this
     */
    public function create(UserRequest $request)
    {
        $user = new User();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('User::users.create_edit')->with(compact('user'));
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {
        try {
            $user = $this->userService->store($request, User::class);

            flash(trans('Corals::messages.success.created', ['item' => $user->name]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, User::class, 'store');
        }

        return redirectTo($this->resource_url);
    }


    /**
     * @param UserRequest $request
     * @param User $user
     * @return $this
     */
    public function show(UserRequest $request, User $user)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $user->full_name])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $user->hashed_id . '/edit']);

        return view('User::users.show')->with(compact('user'));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return $this
     */
    public function edit(UserRequest $request, User $user)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $user->full_name])]);

        return view('User::users.create_edit')->with(compact('user'));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            $user = $this->userService->update($request, $user);

            flash(trans('Corals::messages.success.updated', ['item' => $user->name]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, User::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserRequest $request, User $user)
    {
        try {
            $name = $user->name;

            $this->userService->destroy($request, $user);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $name])];
        } catch (\Exception $exception) {
            log_exception($exception, User::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(BulkRequest $request)
    {
        try {

            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);


            switch ($action) {
                case 'delete':
                    foreach ($selection as $selection_id) {
                        $user = User::findByHash($selection_id);
                        $user_request = new UserRequest;
                        $user_request->setMethod('DELETE');
                        $this->destroy($user_request, $user);
                    }
                    $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
                    break;
            }


        } catch (\Exception $exception) {
            log_exception($exception, User::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}