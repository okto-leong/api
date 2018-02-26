<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Cache;
use App\Model\News;
use App\Model\Topic;
use DB;


class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public static function createNews(Request $request)
    { 
        if ($request->input('title') != '' && $request->input('status') != '') {

            $unique_title = count(News::where('title', '=', $request->input('title'))->get());

            if ($unique_title > 0) {
                return 'This title has already exists';
            }

            $news = new News;            
            $news->title = $request->input('title');
            $news->status = $request->input('status');
            $news->save();

            if(conut($request->input('related_topic')) > 0) {
                $news_id = News::select('id')->where('title', '=', $request->input('title'))->get()->toArray();
                foreach ($request->input('related_topic') as $key => $value) {
                    DB::table('news_topic')->insert(array('news_id' => $news_id[0]['id'], 'topic_id' => $value));
                }
                
            }

            return 'Saved successfully';
        } else {
            return 'Failed to create, please check your data';
        }
        
    }

    public static function updateNews(Request $request) 
    {
        if ($request->input('id') != '') {
            $update_news = News::where('id', $request->input('id'))->firstOrFail();
            $update = array(
                    'title'         => $request->input('title'),
                    'status'        => $request->input('status')            
                );
            
            $update_news->fill($update);
            $update_news->save();

            if(count($request->input('related_topic')) > 0) {
                $related_topics = DB::table('news_topic')->select('topic_id')->where('news_id', $request->input('id'))->get()->toArray();
                
                foreach ($related_topics as $key => $value) {                    
                    $old_topic[] = $value->topic_id; 
                }

                foreach ($request->input('related_topic') as $key => $value) {
                    $new_topic[] = $value;
                }
                
                $result = array_diff($new_topic, $old_topic);
                
                foreach ($result as $key => $value) {
                    DB::table('news_topic')->insert(array('news_id' => $request->input('id'), 'topic_id' => $value));
                }
            }

            return 'Updated successfully';
        } else {
            return 'Failed to update';
        }
    }

    public static function deleteNews(Request $request)
    {
        if ($request->input('id') != '') {
            $delete_news = News::where('id', $request->input('id'))->firstOrFail();
            $delete = array(
                    'status'         => 'deleted'
                );
            
            $delete_news->fill($delete);
            $delete_news->save();            
            return 'Deleted successfully';
        } else {
            return 'Failed to delete';
        }
    }

    public static function filterNews(Request $request) 
    {
        return News::where('status', $request->input('status'))->get()->toArray();
    }

    public static function createTopic(Request $request)
    { 
        if ($request->input('name') != '' && $request->input('status') != '') {

            $unique_name = count(Topic::where('name', '=', $request->input('name'))->get());

            if ($unique_name > 0) {
                return 'This topic has already exists';
            }

            $topic = new Topic;            
            $topic->name = $request->input('name');
            $topic->status = $request->input('status');
            $topic->save();

            if(count($request->input('related_news')) > 0) {
                $topic_id = Topic::select('id')->where('name', '=', $request->input('name'))->get()->toArray();
                foreach ($request->input('related_news') as $key => $value) {
                    DB::table('news_topic')->insert(array('news_id' => $value, 'topic_id' => $topic_id[0]['id']));
                }
                
            }

            return 'Saved successfully';
        } else {
            return 'Failed to create, please check your data';
        }
        
    }

    public static function updateTopic(Request $request) 
    {
        if ($request->input('id') != '') {
            $update_topic = Topic::where('id', $request->input('id'))->firstOrFail();
            $update = array(
                    'name'         => $request->input('name'),
                    'status'        => $request->input('status')            
                );
            
            $update_topic->fill($update);
            $update_topic->save();

            if(count($request->input('related_news')) > 0) {
                $related_news = DB::table('news_topic')->select('news_id')->where('topic_id', $request->input('id'))->get()->toArray();
                
                foreach ($related_news as $key => $value) {                    
                    $old_news[] = $value->news_id; 
                }

                foreach ($request->input('related_news') as $key => $value) {
                    $new_news[] = $value;
                }
                
                $result = array_diff($new_news, $old_news);
                
                foreach ($result as $key => $value) {
                    DB::table('news_topic')->insert(array('news_id' => $value, 'topic_id' => $request->input('id')));
                }
            }

            return 'Updated successfully';
        } else {
            return 'Failed to update';
        }
    }

    public static function deleteTopic(Request $request)
    {
        if ($request->input('id') != '') {
            $delete_topic = Topic::where('id', $request->input('id'))->firstOrFail();
            $delete = array(
                    'status'         => 'deleted'
                );
            
            $delete_topic->fill($delete);
            $delete_topic->save();            
            return 'Deleted successfully';
        } else {
            return 'Failed to delete';
        }
    }

    public static function searchNews(Request $request)
    {
         
        if ($request->input('search') != '') {
            $result = DB::table('news')->select('news.id', 'news.title', 'news.status')
                        ->distinct('news.id')
                        ->join('news_topic', 'news_topic.news_id', '=', 'news.id')
                        ->join('topic', 'news_topic.topic_id', '=', 'topic.id')
                        ->where('topic.name', 'like', '%'.$request->input('search').'%')
                        ->get()
                        ->toArray();

            
            return $result;
        }
    }

}
