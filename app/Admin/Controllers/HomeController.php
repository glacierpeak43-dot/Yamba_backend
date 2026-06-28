<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Forum;
use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {


        return $content
            ->title('Dashboard')
            ->description('Yamba Portal')
            ->row(view('title'))
            ->row(function (Row $row) {

                $user = auth()->user();
                if (!$user->isRole('administrator')) {

                    $row->column(4, function (Column $column) {
                        $blocked_users = User::where('is_blocked_from_system', true)->where('university_id', auth()->user()->university_id)->count();
                        $counsellors = User::where("type", "counsellor")->count();
                        $users = User::where("type", "user")->where('university_id', auth()->user()->university_id)->count();
                        $editors = Administrator::where('university_id', auth()->user()->university_id)->count();
                        $active = User::where('is_blocked_from_system', false)->where('university_id', auth()->user()->university_id)->count();
                        $column->append(
                            view('admin.bar')
                                ->with('users', $users)
                                ->with('blocked_users', $blocked_users)
                                ->with('counsellors', $counsellors)
                                ->with('editors', $editors)
                                ->with('authors', $editors)
                                ->with('active', $active)
                        );
                    });
                    //
                    $row->column(4, function (Column $column) {

                        $forums = array_fill(0, 12, 0);
                        $data = Forum::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')->where('user_id', auth()->user()->id)->groupBy('year', 'month')->get();
                        foreach ($data as $key => $value) {
                            $forums[$value->month - 1] = $value->count;
                        }
                        // dd($forums);
                        $column->append(view('admin.chart')->with('forums', $forums));
                    });
                    //
                    $row->column(4, function (Column $column) {
                        $articles = array_fill(0, 12, 0);
                        $data = Article::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')->where('user_id', auth()->user()->id)->groupBy('year', 'month')->get();
                        foreach ($data as $key => $value) {
                            $articles[$value->month - 1] = $value->count;
                        }
                        $column->append(view('admin.articles')->with('articles', $articles));
                        // $column->append(view('admin.votes'));
                    });
                } else {
                    $row->column(4, function (Column $column) {
                        $blocked_users = User::where('is_blocked_from_system', true)->count() ?? 0;
                        $active = User::where('is_blocked_from_system', false)->count() ?? 0;
                        $counsellors = User::where("type", "counsellor")->count() ?? 0;
                        $users = User::where("type", "user")->count() ?? 0;
                        $editors = Administrator::where('university_id', '!=', null)->count()?? 0;

                        $authors = User::where('is_blocked_from_system', true)->count();
                        $column->append(
                            view('admin.bar')
                                ->with('users', $users)
                                ->with('blocked_users', $blocked_users)
                                ->with('counsellors', $counsellors)
                                ->with('editors', $editors)
                                ->with('authors', $editors)
                                ->with('active', $active)
                        );
                    });
                    //
                    $row->column(4, function (Column $column) {

                        $forums = array_fill(0, 12, 0);
                        $data = Forum::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')->groupBy('year', 'month')->get();
                        foreach ($data as $key => $value) {
                            $forums[$value->month - 1] = $value->count;
                        }
                        $column->append(view('admin.chart')->with('forums', $forums));
                    });
                    //
                    $row->column(4, function (Column $column) {
                        $articles = array_fill(0, 12, 0);
                        $data = Article::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')->groupBy('year', 'month')->get();
                        foreach ($data as $key => $value) {
                            $articles[$value->month - 1] = $value->count;
                        }
                        $column->append(view('admin.articles')->with('articles', $articles));
                        // $column->append(view('admin.votes'));
                    });
                }
                //

            });
    }
}
