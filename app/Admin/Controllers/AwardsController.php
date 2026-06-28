<?php

namespace App\Admin\Controllers;

use App\Models\Awards;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AwardsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Awards';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Awards());

        $grid->column('id', __('Id'));
        $grid->column('points', __('Points'));
        $grid->column('user_id', __('User id'));
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
        $show = new Show(Awards::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('points', __('Points'));

        $show->field('user_id', __('User id'));
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
        $form = new Form(new Awards());
        $users = User::all();

        $form->textarea('points', __('Points'));
        $form->select('user_id', __('Select User '))->options($users->pluck('name', 'id'))->required();

        return $form;
    }
}
