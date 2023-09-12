<?php


namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;


abstract class GenericControllerViews extends Controller
{
    /**
     * The model associated with this controller.
     * Do not add the type model cause then you can't make it work. This because you will get an error on the type when another controller uses this generic controller.
     * @var Model
     */
    protected $model;

    /**
     * Constructor to inject the model into the controller.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource (returns a view).
     *
     * @return View
     */
    public function index(): View
    {
        $items = $this->model->all();
        return view($this->getViewPath('index'), compact('items'));
    }

    /**
     * Show the form for creating a new resource (returns a view).
     *
     * @return View
     */
    public function create(): View
    {
        return view($this->getViewPath('create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request data based on rules defined in getValidationRules
        $validatedData = $request->validate($this->getValidationRules());

        // Create a new item using the validated data
        $this->model->create($validatedData);

        // Redirect to the index view with a success message
        return redirect()->route($this->getRoutePrefix() . '.index')->with('success', 'Item created successfully');
    }

    /**
     * Show the form for editing the specified resource (returns a view).
     *
     * @param mixed $id
     * @return View
     */
    public function edit(mixed $id): View
    {
        $item = $this->model->find($id);
        return view($this->getViewPath('edit'), compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param mixed $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, mixed $id): JsonResponse|RedirectResponse
    {
        // Validate the request data based on rules defined in getValidationRules
        $validatedData = $request->validate($this->getValidationRules());

        // Find the item by ID
        $item = $this->model->find($id);

        // If the item is not found, return a 404 response
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Update the item with the validated data
        $item->update($validatedData);

        // Redirect to the index view with a success message
        return redirect()->route($this->getRoutePrefix() . '.index')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function destroy(mixed $id): JsonResponse
    {
        // Find the item by ID
        $item = $this->model->find($id);

        // If the item is not found, return a 404 response
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Delete the item
        $item->delete();

        // Redirect to the index view with a success message
        return redirect()->route($this->getRoutePrefix() . '.index')->with('success', 'Item deleted successfully');
    }

    /**
     * Generate the view path for the given action.
     *
     * @param string $action
     * @return string
     */
    protected function getViewPath(string $action): string
    {
        return $this->getModelNamePlural() . '.' . $action;
    }

    /**
     * Defined validation rules for the specific model here.
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        // Define the validation rules for the specific model here
        return [];
    }

    /**
     * Generate the route prefix based on the model name (plural).
     *
     * @return string
     */
    protected function getRoutePrefix(): string
    {
        return $this->getModelNamePlural();
    }

    /**
     * Get the lowercase, singular name of the model.
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return strtolower(class_basename($this->model));
    }

    /**
     * Get the lowercase, plural name of the model.
     *
     * @return string
     */
    protected function getModelNamePlural(): string
    {
        return str_plural($this->getModelName());
    }
}