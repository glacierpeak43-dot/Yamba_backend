<?php

namespace App\Admin\Controllers;

use App\Models\ChatReport;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ChatReportsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ChatReport';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ChatReport());

        $grid->filter(function($filter){
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->like('reported_user', 'reported_user');

        });

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User who Reported'))->display(function ($value) {
            $category = User::find($value);
            return $category->name;
        });
        $grid->column('reported_user', __('User Who Assulted'))->display(function ($value) {
            $category = User::find($value);
            return $category->name;
        });
        $grid->column('reported_message', __('Reported message'));
        $grid->column('chat_evidence', __('Chat evidence'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(ChatReport::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('reported_user', __('Reported user'));
        $show->field('reported_message', __('Reported message'));
        $show->field('chat_evidence', __('Chat evidence'));
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
        $form = new Form(new ChatReport());

        $form->number('user_id', __('User id'));
        $form->number('reported_user', __('Reported user'));
        $form->textarea('reported_message', __('Reported message'));
        $form->textarea('chat_evidence', __('Chat evidence'));

        return $form;
    }
}
