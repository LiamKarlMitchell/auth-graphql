<?php

namespace Marqant\AuthGraphQL\GraphQL\Mutations;

use Exception;
use Illuminate\Support\Facades\Hash;
use Tjventurini\GraphQLExceptions\Exceptions\ClientSaveInternalGraphQLException;

/**
 * Class Register
 *
 * @package Marqant\AuthGraphQL\GraphQL\Mutations
 */
class Register
{
    /**
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     *
     * @throws Exception
     */
    public function resolve($rootValue, array $args)
    {
        try {
            $model = app(config('auth.providers.users.model'));
            $name = (empty($args['lastName'])) ? $args['firstName'] : "{$args['firstName']} {$args['lastName']}";
            $input = collect($args)
                ->except('password_confirmation')
                ->toArray();
            $input['name'] = $name;
            $input['password'] = Hash::make($input['password']);
            $model->fill($input);
            $model->save();
            return [
                'accessToken' => $model->createToken($model->id)->plainTextToken,
                'user' => $model,
            ];
        } catch (Exception $exception) {
            throw new ClientSaveInternalGraphQLException($exception);
        }
    }
}
