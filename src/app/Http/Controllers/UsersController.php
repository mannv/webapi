<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UsersController extends BaseController
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserValidator
     */
    protected $validator;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
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
            $params = $request->only([
                'email',
                'password',
                'name',
                'age',
                'address',
                'tel',
                'gender'
            ]);
            $this->validator->with($params)->passesOrFail(ValidatorInterface::RULE_CREATE);
            $params['password'] = Hash::make($params['password']);
            $user = $this->repository->create($params);

            return $this->successResponse($user);
        } catch (ValidatorException $e) {
            return $this->errorResponse($e->getMessageBag(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function show($email)
    {
        try {
            if (!$this->verifyEmail($email)) {
                return $this->errorResponse('Forbidden', Response::HTTP_FORBIDDEN);
            }

            $user = $this->repository->find($email);

            return $this->successResponse($user);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $email
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(Request $request, $email)
    {
        try {
            $params = $request->only([
                'name',
                'age',
                'address',
                'tel',
                'gender'
            ]);

            if (!$this->verifyEmail($email)) {
                return $this->errorResponse('Forbidden', Response::HTTP_FORBIDDEN);
            }

            $this->validator->with($params)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $user = $this->repository->update($params, $email);

            return $this->successResponse($user);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (ValidatorException $e) {
            return $this->errorResponse($e->getMessageBag(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if the email belongs to the current person or not
     * @param $email
     * @return bool
     */
    private function verifyEmail($email)
    {
        $user = Auth::user();
        return $user->email == $email;
    }
}
