<?php

namespace App\Admin\Controllers;

use App\Models\Forum;
use App\Models\ForumCategories;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;

class ForumController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Forum';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Forum());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('forum_category_id', __('Forum category id'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));

        $grid->column('body')->display(function ($value) {
            return substr(strip_tags($value), 0, 100);
        });
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $user = auth()->user();
        if (!$user->isRole('administrator')) {
            $grid->model()->where('user_id', auth()->user()->id);
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
        $show = new Show(Forum::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('forum_category_id', __('Forum category id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('body', __('Body'));
        $show->field('deleted_at', __('Deleted at'));
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
        $form = new Form(new Forum());
        $form->hidden('user_id', __('User id'))->value(1647);
        $form->select('forum_category_id', __('Select Category '))->options(ForumCategories::all()->pluck('name', 'id'))->required();
        $form->text('title', __('Title'))->required();
        $form->text('description', __('Description'))->required();
        $form->ckeditor('body', __('Body'))->required();

        return $form;
    }
}
