<?php

namespace App\Admin\Controllers;

use App\Models\Role;
use App\Models\University;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', 'Name');
            $filter->equal('type', 'Type')->select(['counsellor' => 'Counsellor', 'user' => 'User']);
            $filter->equal('university_id', 'University')->select(University::all()->pluck('name', 'id')->toArray());
        });

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'))->link(function ($user) {
            return '/portal/users/' . $user->id;
        }, '');
        $grid->column('email', __('Email'));
        $grid->column('University', __('university_id'))->display(function () {
            return $this->university->name;
        });
        $grid->column('type', __('Type'));
        $grid->column('email_verified_at', __('Email verified at'));
        $grid->column('password', __('Password'));
        $grid->column('is_blocked_from_system', __('Is blocked from system'));
        $grid->column('remember_token', __('Remember token'));
        $grid->column('referral_code', __('Referral code'));
        $grid->column('referral_count', __('Referral count'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $user = auth()->user();
        if (!$user->isRole('administrator')) {
            $grid->model()->where('university_id', auth()->user()->university_id);
        }
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('type', __('Type'));
        $show->field('type', __('Type'));
        $show->field('profile.title', __('Title'));
        $show->field('profile.bio', __('About'));
        $show->field('profile.phone_number', __('Phone Number'));
        $show->field('email_verified_at', __('Email verified at'));
        // $show->field('password', __('Password'));
        $show->is_blocked_from_system()->as(function ($status) {
            return $status == 0 ? "False" : "True";
        });
        // $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->abuseReports('Abuse Reports', function ($abuseReports) {
            $abuseReports->report();
            $abuseReports->created_at();
            $abuseReports->updated_at();
        });
        $show->articleCommentReports('Article Comment Reports', function ($articleCommentReport) {
            $articleCommentReport->comment()->comment();
            $articleCommentReport->reportedBy()->name();
            $articleCommentReport->created_at();
            $articleCommentReport->updated_at();
        });
        $show->articleCommentReplyReports('Article Comment Reply Reports', function ($articleCommentReplyReport) {
            $articleCommentReplyReport->comment()->comment();
            $articleCommentReplyReport->reportedBy()->name();
            $articleCommentReplyReport->created_at();
            $articleCommentReplyReport->updated_at();
        });
        $show->forumCommentReports('Forum Comment Reports', function ($forumCommentReport) {
            $forumCommentReport->comment()->comment();
            $forumCommentReport->reportedBy()->name();
            $forumCommentReport->created_at();
            $forumCommentReport->updated_at();
        });
        $show->forumCommentReplyReports('Forum Comment Reply Reports', function ($forumCommentReplyReport) {
            $forumCommentReplyReport->comment()->comment();
            $forumCommentReplyReport->reportedBy()->name();
            $forumCommentReplyReport->created_at();
            $forumCommentReplyReport->updated_at();
        });

        $show->awards('Awards', function ($awards) {
            $awards->points();
        });


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $userNew = new User();
        // dd($userNew);
        $form = new Form($userNew);

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));

        $form->select('type', __('Select Type '))->options(Role::all()->pluck('name', 'name'))->required();
        $user = auth()->user();
        $universities = !$user->isRole('administrator') ? University::where('id', $user->university_id) : University::all();
        $form->select('university_id', __('Select University '))->options($universities->pluck('name', 'id'))->required();
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->switch('is_blocked_from_system', __('Is blocked from system'));
        $form->text('remember_token', __('Remember token'));
        $form->text('profile.title', __('User Title'));
        $form->textarea('profile.bio', __('Bio'));
        $form->text('profile.phone_number', __('Phone Number'));
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });
        return $form;
    }
}
