<?php

namespace App\Http\Controllers;

/**
 * Class LogoutController.
 *
 * @package namespace App\Http\Controllers;
 */
class LogoutController extends BaseController
{
    /**
     * destroys jwt token
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->logout(true);
        return $this->successResponse([
            'logout' => true,
            'message' => 'logout successful'
        ]);
    }

}
