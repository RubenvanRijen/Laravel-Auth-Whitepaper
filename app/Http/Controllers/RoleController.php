<?php

namespace App\Http\Controllers;

use App\Http\Documentation\RoleControllerDocumentation;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class RoleController extends GenericController implements RoleControllerDocumentation
{
    /**
     * The Eloquent model associated with this controller.
     *
     * @var Model
     */
    protected $model = Role::class;

    /**
     * Get validation rules for the specific model.
     *
     * @return array
     */
    protected function getValidationRulesCreate(): array
    {
        // Define the validation rules for the specific model here.
        return [
            'name' => 'required|max:255|min:3|unique:roles',
        ];
    }

    /**
     * Get validation rules for the specific model for updating.
     *
     * @return array
     */
    protected function getValidationRulesUpdate(mixed $id): array
    {
        // Define the validation rules for the specific model here when updating.
        return [
            'name' => 'required|unique:roles,name,' . $id . '|max:255',
        ];
    }
}