<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

abstract class GenericController extends Controller
{
    /**
     * The Eloquent model associated with this controller.
     *
     * @var Model
     */
    protected $model;

    /**
     * Return all the data of the associated model
     *  @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = $this->model::paginate(10);
        return response()->json(['data' => $data], 200);
    }

    /**
     * Store a new item in the associated model.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data using rules defined in getValidationRules method.
        $validatedData = $request->validate($this->getValidationRulesCreate());

        // Create a new model instance with the validated data.
        $item = $this->model::create($validatedData);

        // Return a JSON response with the created item and a status code of 201 (Created).
        return response()->json($item, 201);
    }

    /**
     * Retrieve a specific item from the associated model by its ID.
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function show(mixed $id): JsonResponse
    {
        $item = $this->model::find($id);

        if (!$item) {
            // Return a JSON response with a 404 (Not Found) status if the item is not found.
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Return a JSON response with the found item.
        return response()->json($item, 200);
    }

    /**
     * Update a specific item in the associated model by its ID.
     *
     * @param  Request $request
     * @param  mixed $id
     * @return JsonResponse
     */
    public function update(Request $request, mixed $id): JsonResponse
    {
        $item = $this->model::find($id);

        if (!$item) {
            // Return a JSON response with a 404 (Not Found) status if the item is not found.
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Validate the incoming request data using rules defined in getValidationRules method.
        $validatedData = $request->validate($this->getValidationRulesUpdate($id));

        // Update the item with the validated data.
        $item->update($validatedData);

        // Return a JSON response with the updated item.
        return response()->json($item);
    }

    /**
     * Get validation rules for the specific model for create.
     *
     * @return array
     */
    protected function getValidationRulesCreate(): array
    {
        // Define the validation rules for the specific model here when creating.
        return [];
    }

    /**
     * Get validation rules for the specific model for updating.
     *
     * @return array
     */
    protected function getValidationRulesUpdate(mixed $id): array
    {
        // Define the validation rules for the specific model here when updating.
        return [];
    }

    /**
     * Delete a specific item from the associated model by its ID.
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy(mixed $id): JsonResponse
    {
        $item = $this->model::find($id);

        if (!$item) {
            // Return a JSON response with a 404 (Not Found) status if the item is not found.
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Delete the item.
        $item->delete();

        // Return a JSON response with a 204 (No Content) status indicating success.
        return response()->json(null, 204);
    }
}
