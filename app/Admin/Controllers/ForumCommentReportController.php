<?php

namespace App\Admin\Controllers;

use App\Models\ForumCommentReport;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ForumCommentReportController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ForumCommentReport';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ForumCommentReport());

        $grid->column('id', __('Id'));
        $grid->column('user.name', __('User'))->link(function ($user) {
            return '/portal/users/' . $user->user_id;
        }, '');
        $grid->column('reportedBy.name', __('Reported by'))->link(function ($user) {
            return '/portal/users/' . $user->reported_by;
        }, '');
        $grid->column('comment.comment', __('Comment'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {

            $actions->disableEdit();
        });
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
        $show = new Show(ForumCommentReport::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('reported_by', __('Reported by'));
        $show->field('comment_id', __('Comment id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ForumCommentReport());

        $form->number('user_id', __('User id'));
        $form->number('reported_by', __('Reported by'));
        $form->number('comment_id', __('Comment id'));

        return $form;
    }
}
