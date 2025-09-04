<?php
namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;

class BlogService
{
    public function create(array $data): Blog
    {
        #1. sort
        $maxSort = Blog::max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        #2. insert
        $blog = Blog::create($data);

        return $blog;
    }

    public function updateModel(Blog $blog, array $data): Blog
    {
        $blog->title = $data['title'];
        $blog->content = $data['content'];
        $blog->save();

        return $blog;
    }

    public function getBlogs(string $sort = 'desc', int $page = 1): array
    {
        $perPage = 10;

        $results = Blog::query()
            ->where('is_active', true)
            ->orderBy('created_at', $sort)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'items' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ]
        ];
    }

    public function search(string $keyword, int $page = 1)
    {
        $perPage = 10;

        $results = Blog::query()
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'items' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ]
        ];
    }

    public function setActive(Blog $blog, bool $active): Blog
    {
        $blog->is_active = $active;
        $blog->save();

        return $blog;
    }

    public function delete(Blog $blog): void
    {
        $blog->delete();
    }

    public function reorder(Blog $blog, int $newPosition): Blog
    {
        #FLOAT SORT

        #1. sort
        $blogs = Blog::orderBy('sort_order')->get(['id', 'sort_order']);

        #2. validate new position
        if ($newPosition < 1 || $newPosition > $blogs->count()) {
            return ResponseService::error('Invalid position', 400);
        }

        #3. prev & next Blog
        $prevBlog = $blogs->get($newPosition - 2);
        $nextBlog = $blogs->get($newPosition - 1);

        #4. caculate sort_order
        if ($prevBlog && $nextBlog) {
            // between
            $blog->sort_order = ($prevBlog->sort_order + $nextBlog->sort_order) / 2;
        } elseif (!$prevBlog && $nextBlog) {
            // front
            $blog->sort_order = $nextBlog->sort_order / 2;
        } elseif ($prevBlog && !$nextBlog) {
            // end
            $blog->sort_order = $prevBlog->sort_order + 1;
        } else {
            // only one
            $blog->sort_order = 1;
        }

        #5. 
        $blog->save();

        #6.
        return $blog;
    }
}