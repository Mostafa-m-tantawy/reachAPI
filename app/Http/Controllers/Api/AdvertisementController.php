<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdvertisementController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {

        $advertisements = Advertisement::query();
        if($request->category_id)
            $advertisements =$advertisements->where('category_id',$request->category_id);

        if($request->tag_id)
            $advertisements =$advertisements->whereHas('tags',function ($q)use($request){
                $q->where('tags.id',$request->tag_id);
            });
        $advertisements=$advertisements->get();
        $categories = Category::all();
        $tags = Tag::all();
        return response()->json(compact('advertisements','categories','tags'), 200);

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
            'title' => ['required', 'max:255'],
            'type' => ['required', 'max:255','in:free,paid'],
            'description' => ['nullable',],
            'start_date' => ['nullable'],
            'category_id' => ['nullable','exists:categories,id'],
            'user_id' => ['required','exists:users,id'],
            'tags.*' => ['required','exists:tags,id'],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $advertisement= Advertisement::create($validator->valid());
            $advertisement->tags()->sync($request->tags);


            DB::commit();
            return $this->apiResponse(null, null, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());
            return $this->apiResponse(null, [$exception->getMessage()], 422);
        }
    }


    /**
     * Show the  the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Advertisement $advertisement)
    {
        return response()->json(compact('advertisement'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Advertisement $advertisement)
    {
        return response()->json(compact('advertisement'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:255'],
            'type' => ['required', 'max:255','in:free,paid'],
            'description' => ['nullable',],
            'start_date' => ['nullable'],
            'category_id' => ['nullable','exists:categories,id'],
            'user_id' => ['required','exists:users,id'],
            'tags.*' => ['required','exists:tags,id'],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $advertisement->update($validator->valid());
            $advertisement->tags()->sync($request->tags);
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
    public function destroy(Advertisement $advertisement)
    {
        try {
            DB::beginTransaction();

            $advertisement->delete();
            DB::commit();
            return $this->apiResponse(null, null, 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::debug($exception->getMessage());
            return $this->apiResponse(null, [$exception->getMessage()], 422);

        }
    }
}
