<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    private $post;
    private $category;

    public function __construct()
    {
        $this->post = new Post();
        $this->category = new Category();
    }

    /**
     * 投稿リスト
     * 
     * @param int $id ユーザーID
     * @return Response src/resources/views/user/list/index.blade.phpを表示
     */
    public function index(int $id)
    {
        // ユーザーIDと一致する投稿データを取得
        $posts = $this->post->getAllPostsByUserId($id);
        return view('user.list.index', compact(
            'posts',
        ));
    }

    /**
     * 記事投稿画面
     */
    public function create()
    {
        $categories = $this->category->getAllCategories();
        return view('user.list.create', compact(
            'categories',
        ));
    }

    /**
     * 記事投稿処理
     * 
     * @param string $request リクエストデータ
     * @return Response src/resources/views/user/list/index.blade.phpを表示
     */
    public function store(PostRequest $request)
    {
        // ログインしているユーザー情報を取得
        $user = Auth::user();
        // ログインユーザー情報からユーザーIDを取得
        $user_id = $user->id;


        switch (true) {
            // 下書き保存クリック時の処理
            case $request->has('save_draft'):
                $this->post->insertPostToSaveDraft($user_id, $request);
                break;
            // 公開クリック時の処理
            case $request->has('release'):
                $this->post->insertPostToRelease($user_id, $request);
                break;
            // 予約公開クリック時の処理
            case $request->has('reservation_release'):
                $this->post->insertPostToReservationRelease($user_id, $request);
                break;
            // 上記以外の処理
            default:
                $this->post->insertPostToSaveDraft($user_id, $request);
                break;
        }

        return to_route('user.index', ['id' => $user_id]);
    }

    /**
     * 記事詳細
     * 
     * @param int $post_id 投稿ID
     * @return Response src/resources/views/user/list/show.blade.phpを表示
     */
    public function show($post_id) {
        // リクエストされた投稿IDをもとにpostsテーブルから一意のデータを取得
        $showPostData = $this->post->feachPostDateByPostId($post_id);
        return view('user.list.show', compact(
            'showPostData',
        ));
    }

    /**
     * 記事編集
     *
     * @param int $post_id 投稿ID
     * @return Response src/resources/views/user/list/edit.blade.phpを表示
     */
    public function edit($post_id)
    {
        // カテゴリーデータを全件取得
        $categories = $this->category->getAllCategories();
        // 投稿IDをもとに特定の投稿データを取得
        $post = $this->post->feachPostDateByPostId($post_id);
        return view('user.list.edit', compact(
            'categories',
            'post',
        ));
    }

    /**
     * 記事の更新
     *
     * @param int $post_id 投稿ID
     * @return Response src/resources/views/user/list/index.blade.phpを表示
     */
    public function update(PostRequest $request, $post_id)
    {
        // ログインしているユーザー情報を取得
        $user = Auth::user();
        // ログインユーザー情報からユーザーIDを取得
        $user_id = $user->id;

        // 投稿IDをもとに特定の投稿データを取得
        $post = $this->post->feachPostDateByPostId($post_id);

        switch (true) {
            // 下書き保存クリック時の処理
            case $request->has('save_draft'):
                $this->post->updatePostToSaveDraft($request, $post);
                break;
            // 公開クリック時の処理
            case $request->has('release'):
                $this->post->updatePostToRelease($request, $post);
                break;
            // 予約公開クリック時の処理
            case $request->has('reservation_release'):
                $this->post->updatePostToReservationRelease($request, $post);
                break;
            // 上記以外の処理
            default:
                $this->post->updatePostToSaveDraft($request, $post);
                break;
        }

        return to_route('user.index', ['id' => $user_id]);
    }
}
