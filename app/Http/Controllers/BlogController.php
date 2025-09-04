<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;  
use App\Services\BlogService;
use App\Services\ResponseService;

class BlogController extends Controller
{
    protected BlogService $blogService;
    const USER_ID = 0; // no auth yet, using a default user_id

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    # POST: /blog -> Create
    public function store(Request $request)
    {
        try {
            #1. validation
            $data = $request->validate([
                'title'   => 'required|string|max:255',
                'content' => 'required|string'
            ]);
            $data['user_id'] = self::USER_ID;

            #2. create
            $blog = $this->blogService->create($data);

            #3.
            return ResponseService::success($blog, 'Blog created successfully');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # GET: /api/blogs/search?keyword=xxx
    public function search(Request $request)
    {
        try {
            #1. validation
            $request->validate([
                'keyword' => 'required|string|max:255',
                'page'    => 'sometimes|integer|min:1',
            ]);

            #2. input
            $keyword = $request->input('keyword');
            $page = $request->input('page', 1);

            #3. service
            $data = $this->blogService->search($keyword, $page);

            if (empty($data['items'])) {
                return ResponseService::error('Blog not found', 404);
            }

            #4. response
            return ResponseService::success($data, 'Search results');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # GET: /blogs -> Sort (time)
    public function index(Request $request)
    {
        try {
            #1. query params
            $page = (int) $request->query('page', 1);
            $sort = $request->query('sort', 'desc'); // support asc / desc

            #2. service
            $data = $this->blogService->getBlogs($sort, $page);

            #3. response
            return ResponseService::success($data, 'Public blogs list');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # PUT: /blogs/{blog} → Update
    # {blog} Route Model Binding findOrFail() (if not found -> 404)
    public function update(Request $request, Blog $blog)
    {
        try {
            #1. validation
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            #2. 
            $blog = $this->blogService->updateModel($blog, $data);

            #3.
            return ResponseService::success($blog, 'Blog updated successfully');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # PATCH: /blogs/{blog}/active → Set Active & Set Inactive
    public function setActive(Request $request, Blog $blog)
    {
        try {
            #1.
            $active = $request->input('is_active');
            if (!is_bool($active)) {
                return ResponseService::error('is_active must be boolean', 400);
            }

            #2.
            $blog = $this->blogService->setActive($blog, $active);

            #3.
            return ResponseService::success($blog, 'updated');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # DELETE: /blogs/{blog} → destory (Delete)
    public function destroy(Blog $blog)
    {
        try {
            #1.
            $this->blogService->delete($blog);

            #2.
            return ResponseService::success(null, 'Blog deleted');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    }

    # PATCH /blogs/{blog}/reorder → reorder (Ordering)
    public function reorder(Request $request, Blog $blog)
    {
        try {
            #1. validate
            $data = $request->validate([
                'new_position' => 'required|integer|min:1'
            ]);

            #2. call service
            $blog = $this->blogService->reorder($blog, $data['new_position']);

            #3. response
            return ResponseService::success($blog, 'Blog reordered successfully');
        } catch (\Throwable $e) {
            return ResponseService::exception($e);
        }
    } 
}