<?php

namespace App\Validators;

use App\Entities\User;
use Illuminate\Contracts\Validation\Factory;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class UserValidator.
 *
 * @package namespace App\Validators;
 */
class UserValidator extends LaravelValidator
{
    public function __construct(Factory $validator)
    {
        parent::__construct($validator);

        $this->rules = [
            ValidatorInterface::RULE_CREATE => [
                'email' => 'required|unique:users,email',
                'password' => 'required|min:6',
                'name' => 'required',
                'age' => 'nullable|integer|min:1|max:70',
                'address' => 'nullable',
                'tel' => 'nullable',
                'gender' => 'nullable|in:' . implode(',', [User::GENDER_FEMALE, User::GENDER_MALE])
            ],
            ValidatorInterface::RULE_UPDATE => [
                'name' => 'nullable',
                'age' => 'nullable|integer|min:1|max:70',
                'address' => 'nullable',
                'tel' => 'nullable',
                'gender' => 'nullable|in:' . implode(',', [User::GENDER_FEMALE, User::GENDER_MALE])
            ],
        ];
    }
}
