<?php

namespace Poshub\Ecommerce\Repositories;

use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\Blog;

class BlogRepository
{
      public function getData($request, Array $oderBy)
      {
            return Blog::where(function ($q) use ($request) {
                  return !empty($request->name) ? $q->where('title', 'like', '%' . $request->name . '%') : '';
            })->where(function ($q) use ($request) {
                  return !empty($request->category) ? $q->where('category_id', $request->category) : '';
            })->orderBy($oderBy['value'], $oderBy['type']);
      }

      public function getDetail(Blog $blog)
      {
            $blog->update([
                  'views'     => $blog->views + 1
            ]);

            return $blog;
      }
}
