<?php

namespace App\Http\Controllers;

use App\ProductApproveCode;
use App\ShopProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use function foo\func;

class ShopProductController extends Controller
{
    public function approve($id, $approveCode)
    {
        $product = ShopProduct::where('id', $id)->whereHas('approveCode', function (Builder $query) use ($approveCode) {
            $query->where('approve_code', $approveCode);
        })->first();

        if (!empty($product)) {
            $product->status = true;
            $product->approval_date = Carbon::now();
            $product->save();

            $code = ProductApproveCode::where([
               ['shop_product_id', $id],
               ['approve_code', $approveCode]
            ]);
            $code->delete();
        } else {
            return Redirect::back()->withErrors([
                'approvalCodeError' => 'Approval code not found!'
            ]);
        }

        return Redirect::route('productDetails', ['id' => $id]);
    }
}
