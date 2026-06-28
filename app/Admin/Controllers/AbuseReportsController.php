<?php

namespace App\Admin\Controllers;

use App\Models\AbuseReports;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AbuseReportsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AbuseReports';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AbuseReports());

        $grid->column('id', __('Id'));
        $grid->column('report', __('Report'));
        $grid->column('user_id', __('User id'))->display(function ($value) {
            $category = User::find($value);

            return $category->name;
        });;
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
        $show = new Show(AbuseReports::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('report', __('Report'));
        $show->field('user_id', __('User id'))->display(function ($value) {
            $category = User::find($value);
            return $category->name;
        });
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
        $form = new Form(new AbuseReports());
//        $form->textarea('report', __('Report'));
//        $form->number('user_id', __('User id'));

        return $form;
    }
}
