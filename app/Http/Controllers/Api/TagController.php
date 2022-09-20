<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json(compact('tags'), 200);

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
            'name' => ['required', 'max:255', 'unique:tags,name']
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            Tag::create($validator->valid());
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
    public function edit(Tag $tag)
    {
        return response()->json(compact('tag'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:255', 'unique:tags,name,' . $tag->id]
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $tag->update($validator->valid());
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
    public function destroy(Tag $tag)
    {
        try {
            DB::beginTransaction();

            $tag->delete();
            DB::commit();
            return $this->apiResponse(null, null, 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());
            return $this->apiResponse(null, [$exception->getMessage()], 422);

        }
    }
}
