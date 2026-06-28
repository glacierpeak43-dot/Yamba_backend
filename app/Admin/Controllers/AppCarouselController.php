<?php

namespace App\Admin\Controllers;

use App\Models\AppCarouselPictures;
use App\Models\University;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AppCarouselController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AppCarouselPictures';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AppCarouselPictures());

        $grid->column('id', __('Id'));
        $grid->column('image_path', __('Image '))->image();
        $grid->column('university_id', __('University id'))->display(function ($value) {
            $category = University::find($value);
            return $category->name;
        });
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
        $show = new Show(AppCarouselPictures::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('image_path', __('Image path'));
        $show->field('university_id', __('University id'));
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
        $form = new Form(new AppCarouselPictures());
        $form->image('image_path', __('Image path'))->required();
        $user = auth()->user();
        $universities = !$user->isRole('administrator') ? University::where('id', $user->university_id) : University::all();
        $form->select('university_id', __('Select University '))->options($universities->pluck('name', 'id'))->required();

        return $form;
    }
}
