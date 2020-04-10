<?php

namespace Corals\User\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\User\Http\Requests\UserRequest;
use Corals\User\Facades\TwoFactorAuth;

class UserService extends BaseServiceClass
{

    protected $excludedRequestParams = [
        'picture', 'channel', 'two_factor_auth_enabled',
        'password_confirmation', 'roles', 'clear', 'confirmed',
    ];

    public function getRequestData($request)
    {
        $data = $request->except($this->excludedRequestParams);

        if (
            ($request->has('confirmed') || !\Settings::get('confirm_user_registration_email', false))
            && is_null(optional($this->model)->confirmed_at)
        ) {
            $data['confirmed_at'] = now();
        } elseif (!$request->has('confirmed') && \Settings::get('confirm_user_registration_email', false)) {
            $data['confirmed_at'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    public function setTwoFactorAuthDetails(UserRequest $request)
    {
        if (!TwoFactorAuth::isActive()) {
            return;
        }

        if (!TwoFactorAuth::isRegistered($this->model)) {
            TwoFactorAuth::register($this->model);
        }


        $twoFactorOptions = $this->model->getTwoFactorAuthProviderOptions();

        $twoFactorOptions['channel'] = $request->get('channel');

        $twoFactorOptions['enabled'] = $request->get('two_factor_auth_enabled') ? true : false;

        $this->model->two_factor_options = json_encode($twoFactorOptions);

        $this->model->save();
    }


    public function handleUserPicture(UserRequest $request)
    {
        if ($request->has('clear') || $request->hasFile('picture')) {
            $this->model->clearMediaCollection('user-picture');
        }

        if ($request->hasFile('picture') && !$request->has('clear')) {
            $this->model->addMedia($request->file('picture'))
                ->withCustomProperties(['root' => 'user_' . $this->model->hashed_id])
                ->toMediaCollection('user-picture');
        }
    }

    public function handleUserRoles(UserRequest $request)
    {
        $this->model->roles()->sync($request->roles);
    }

    public function store($request, $modelClass, $additionalData = [])
    {
        $data = array_merge($this->getRequestData($request), $additionalData);

        $user = $modelClass::query()->create($data);

        $this->model = $user;

        $this->setTwoFactorAuthDetails($request);

        $this->handleUserPicture($request);

        $this->handleUserRoles($request);

        return $user;
    }

    public function update($request, $user, $additionalData = [])
    {
        $this->model = $user;

        $data = array_merge($this->getRequestData($request), $additionalData);

        $this->model->update($data);

        $this->setTwoFactorAuthDetails($request);

        $this->handleUserPicture($request);

        $this->handleUserRoles($request);

        return $user;
    }

    /**
     * @param $request
     * @param $user
     * @throws \Exception
     */
    public function destroy($request, $user)
    {
        if (user()->id == $user->id) {
            throw new \Exception(trans('User::exception.invalid_destroy_user'));
        }

        $user->delete();
    }
}