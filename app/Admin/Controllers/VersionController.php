<?php

namespace App\Admin\Controllers;

use App\Models\ForcedUpdatesVersions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VersionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ForcedUpdatesVersions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ForcedUpdatesVersions());

        $grid->column('id', __('Id'));
        $grid->column('ios_version', __('Ios version'));
        $grid->column('android_version', __('Android version'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));

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
        $show = new Show(ForcedUpdatesVersions::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('ios_version', __('Ios version'));
        $show->field('android_version', __('Android version'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ForcedUpdatesVersions());

        $form->text('ios_version', __('Ios version'));
        $form->text('android_version', __('Android version'));

        return $form;
    }
}
