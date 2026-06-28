<?php

namespace App\Admin\Controllers;

use App\Models\DeviceToken;
use App\Models\Notifications;
use App\Models\Notifires;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Facades\FCM;

class NotifireController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Notifire';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notifires());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('message', __('Message'));
        $grid->column('from', __('From'));
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
        $show = new Show(Notifires::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('message', __('Message'));
        $show->field('from', __('From'));
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
        $form = new Form(new Notifires());

        $form->text('title', __('Title'))->required();
        $form->text('message', __('Message'))->required();
        $form->text('from', __('From'))->required();
        $form->saving(function (Form $form) {
            $devices = [];
            $tokens = DeviceToken::all();
            $users = User::all();
            foreach ( $users as $user ) {
                Notifications::create([
                    'type' => $form->title,
                    'message' => $form->message,
                    'user_id' => 1,
                ]);
            }

            foreach ($tokens as $token) {
                $devices[] = $token->device_token;
            }

            $messaging = app('firebase.messaging');

            try{
                $message = [
                    'notification' => [
                        'title' => $form->title,
                        'body' => $form->message,
                    ],

                    'data' => [
                        "type" => 'admin',
                        "model" => 'admin',
                    ]
                ];
                $messaging->sendMulticast($message, $devices);

                return true;
            }catch (\Exception $e){
                return false;

            }
        });

        return $form;
    }
}
