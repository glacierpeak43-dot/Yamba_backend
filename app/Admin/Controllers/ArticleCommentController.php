<?php

namespace App\Admin\Controllers;

use App\Models\ArticleComment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ArticleCommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ArticleComment';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ArticleComment());

        $grid->column('id', __('Id'));
        $grid->column('comment', __('Comment'));
        $grid->column('user_id', __('User id'));
        $grid->column('article_id', __('Article id'));
        $grid->column('deleted_at', __('Deleted at'));
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
        $show = new Show(ArticleComment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('comment', __('Comment'));
        $show->field('user.name', __('User'));
        $show->field('article_id', __('Article id'));
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
        $form = new Form(new ArticleComment());

        $form->textarea('comment', __('Comment'));
        $form->number('user_id', __('User id'));
        $form->number('article_id', __('Article id'));

        return $form;
    }
}
