<?php
namespace App\Repositories\Contracts;

interface AuthRepositoryInterface {

    /**
     *
     * Handle action login of user.
     *
     * @param $r
     * @param object
     * @return boolean
     */
    public function doLogin($r, $guard = null);

    /**
     *
     * Handle action login of user.
     *
     * @param array $params
     * @param object
     * @return boolean
     */
    public function register(array $params);
}
