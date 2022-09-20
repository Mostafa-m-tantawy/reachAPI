<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(compact('categories'), 200);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255', 'unique:Categories,name']
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            Category::create($validator->valid());
            DB::commit();
            return $this->apiResponse(null, null, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());
            return $this->apiResponse(null, [$exception->getMessage()], 422);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return response()->json(compact('category'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255', 'unique:tags,name,' . $category->id]
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $category->update($validator->valid());
            DB::commit();

            return $this->apiResponse(null, null, 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());

            return $this->apiResponse(null, [$exception->getMessage()], 422);


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            $category->delete();
            DB::commit();
            return $this->apiResponse(null, null, 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());
            return $this->apiResponse(null, [$exception->getMessage()], 422);

        }
    }
}
