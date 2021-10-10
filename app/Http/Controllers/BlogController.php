<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\UserBlog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function getUserBlogs(Request $request) {
        $data = $request->all();
        $filters = $data['blogFilters'];
        $result['userBlogs'] = UserBlog::getUserBlogs($filters);
        $result['blogCategories'] = BlogCategory::getBlogCategories();
        return response($result);
    }

    public function createNewBlog(Request $request) {
        $data = $request->all();
        $blogFile = $request->file('blogFile');
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $result['status'] = 'error';
        // user can have up to 3 blogs waiting for an approval at the same time to prevent spam
        $userBlogs = UserBlog::getUserAwaitingBlogsCount($userData->id);
        if($userBlogs >= 3) {
            $result['message'] = 'blogLimitReachedError';
            return response($result);
        }

        if(isset($data['content']) && !empty($data['content'])) {

            $newBlog = new UserBlog();
            $newBlog->title = $data['blogTitle'];
            $newBlog->creator_id = $userData->id;
            $newBlog->content = $data['content'];
            $newBlog->category = $data['blogCategory'];

            try {
                $newBlog->save();
            } catch (\Exception $e) {
                $result['message'] = 'blogCreationError';
                $result['error'] = $e->getMessage();
                return response($result);
            }

            if($blogFile) {
                try {
                    $path = $blogFile->storeAs(
                        'blogImages/' . $userData->id, 'blog_thumbnail_' . $newBlog->id . '.jpg'
                    );

                    $newBlog->image_path = $path;
                    $newBlog->save();
                } catch (\Exception $e) {
                    $result['message'] = 'blogFileUploadError';
                    $result['error'] = $e->getMessage();
                    return response($result);
                }
            }
        } else {
            $result['message'] = 'invalidBlogContentError';
            return response($result);
        }

        $result['status'] = 'success';
        $result['message'] = 'blogCreationSuccess';
        return response($result);
    }
}
