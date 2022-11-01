<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Category;
use App\ShopProduct;
use App\ShopProfile;
use App\UserProfileCategory;
use Response;
use DB;

class CategoryController extends BaseController {

	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$userrole = \Auth::user()->role;
		if($userrole != 'admin'){
			\Auth::logout();
			return redirect(route('login'));
		}
        $categories = Category::with('parent')->latest()->paginate(10);
        //dd($categories);
        return view('categories.index',compact('categories'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorylist = Category::with('childs')->where('parent_id',0)->get();
        
        return view('categories.create',compact('categorylist'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required'
        ]);
        $request['parent_id'] = empty($request['parent_id']) ? 0 : $request['parent_id'];
        Category::create($request->all());

        return redirect()->route('categories.index')
                        ->with('success','Category created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('categories.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $categorylist = \DB::table('categories')->where('parent_id',0)->get();
        return view('categories.edit',compact('category','categorylist'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        request()->validate([
            'title' => 'required'
        ]);
        $request['parent_id'] = empty($request['parent_id']) ? 0 : $request['parent_id'];

        $category->update($request->all());

        return redirect()->route('categories.index')
                        ->with('success','Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success','Category deleted successfully');
    }
    
    
     
     /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function ajaxSubcategory(Request $request)
    {
        $input = $request->all();
        $subcategory = \DB::table('categories')->where('parent_id',$input['category_id'])->get();
        return response()->json(['success'=>'Got Simple Ajax Request.','subcategory'=>$subcategory]);
    }
	
	public function getcategory()
    {
		$parent_id = isset($parent_id)?$parent_id:0;
        $mailcategory = Category::where('parent_id',$parent_id)->get();	
		$i = 0;
        foreach($mailcategory as $value){
//            $productsCount = DB::table('shop_products')->where('type_id',$value->id)->count();
//            if ($productsCount > 0) {
                $category[$i]['id'] = $value->id;
                $category[$i]['title'] = $value->title;
                $category[$i]['parent_id'] = $value->parent_id;
                $subcategory = Category::select('id','title','parent_id')->where('parent_id',$value->id)->get();
                $category[$i]['subcategories'] = $subcategory;
//                $category[$i]['products'] = $productsCount;
//                }
            $i++;
        }
        if($category != null){			
			return Response::json(array('error' => false, 'message' => 'Get Category', 'request' => $category), 202);
		} else {            
			return Response::json(array('error' => true, 'errors' => 'Data not found'), 200);
		}
    }
	
	public function getusercategory()
    {
        $user_category = UserProfileCategory::get();	
		
        if($user_category != null){			
			return Response::json(array('error' => false, 'message' => 'Get User Category', 'request' => $user_category), 202);
		} else {            
			return Response::json(array('error' => true, 'errors' => 'Data not found'), 200);
		}
    }
}
