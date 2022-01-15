<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Prettus\Validator\Exceptions\ValidatorException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class LoginController.
 *
 * @package namespace App\Http\Controllers;
 */
class LoginController extends BaseController
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * LoginController constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->getMessageBag(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            try {
                // attempt to verify the credentials and create a token for the user
                if (!$token = \JWTAuth::attempt($credentials)) {
                    return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
                }
            } catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return $this->errorResponse('Could not createtoken', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->successResponse(['data' => $token]);
        } catch (ValidatorException $e) {
            return $this->errorResponse($e->getMessageBag(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
