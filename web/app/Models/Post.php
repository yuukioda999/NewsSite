<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Support\Carbon;

class Post extends Model
{
    use HasFactory;

    /**
     * モデルに関連付けるテーブル
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body',
        'publish_flg',
        'view_counter',
        'favorite_counter',
        'delete_flg',
        'created_at',
        'updated_at'
    ];

    /**
     * Categoryモデルとリレーション
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ユーザーIDに紐づいた投稿リストを全て取得する
     * 
     * @param int $user_id ユーザーID
     * @return Post
     */
    public function getAllPostsByUserId($user_id)
    {
        $result = $this->where('user_id', $user_id)->with('category')->get();
        return $result;
    }

    /**
     * 下書き保存=>publish_flg=0
     * リクエストされたデータをpostsテーブルにinsertする
     * 
     * @param int $user_id ログインユーザーID
     * @param array $request リクエストデータ
     * @return object $result App\Models\Post
     */
    public function insertPostToSaveDraft($user_id, $request)
    {
        $result = $this->create([
            'user_id'          => $user_id,
            'category_id'      => $request->category,
            'title'            => $request->title,
            'body'             => $request->body,
            'publish_flg'      => 0,
            'view_counter'     => 0,
            'favorite_counter' => 0,
            'delete_flg'       => 0,
            // created_atやupdated_atはmDB登録時に自動的に今日の日時で登録されるので、記載しない
        ]);
        return $result;
    }

    /**
     * 公開=>publish_flg=1
     * リクエストされたデータをpostsテーブルにinsertする
     * 
     * @param int $user_id ログインユーザーID
     * @param array $request リクエストデータ
     * @return object $result App\Models\Post
     */
    public function insertPostToRelease($user_id, $request)
    {
        $result = $this->create([
            'user_id'          => $user_id,
            'category_id'      => $request->category,
            'title'            => $request->title,
            'body'             => $request->body,
            'publish_flg'      => 1,
            'view_counter'     => 0,
            'favorite_counter' => 0,
            'delete_flg'       => 0,
            // created_atやupdated_atはmDB登録時に自動的に今日の日時で登録されるので、記載しない
        ]);
        return $result;
    }

    /**
     * 予約公開=>publish_flg=2
     * リクエストされたデータをpostsテーブルにinsertする
     * 
     * @param int $user_id ログインユーザーID
     * @param array $request リクエストデータ
     * @return object $result App\Models\Post
     */
    public function insertPostToReservationRelease($user_id, $request)
    {
        $result = $this->create([
            'user_id'          => $user_id,
            'category_id'      => $request->category,
            'title'            => $request->title,
            'body'             => $request->body,
            'publish_flg'      => 2,
            'view_counter'     => 0,
            'favorite_counter' => 0,
            'delete_flg'       => 0,
            // created_atやupdated_atはmDB登録時に自動的に今日の日時で登録されるので、記載しない
        ]);
        return $result;
    }
}
