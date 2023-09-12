<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
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
     * Get a collection of all items in the associated model.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->model::all();
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
        $validatedData = $request->validate($this->getValidationRules());

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
        return response()->json($item);
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
        $validatedData = $request->validate($this->getValidationRules());

        // Update the item with the validated data.
        $item->update($validatedData);

        // Return a JSON response with the updated item.
        return response()->json($item);
    }

    /**
     * Get validation rules for the specific model.
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        // Define the validation rules for the specific model here.
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