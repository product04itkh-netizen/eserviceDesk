<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Alert Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain alert messages for various scenarios
    | during CRUD operations. You are free to modify these language lines
    | according to your application's requirements.
    |
    */

    'backend' => [
        'roles' => [
            'created' => 'The role was successfully created.',
            'deleted' => 'The role was successfully deleted.',
            'updated' => 'The role was successfully updated.',
        ],
        'service'=>[
          'updated'=> 'The Service was successfully updated.',
          'created'=> 'The Service was successfully created.',
          'deleted' => 'The Services was successfully Removed.',
          'assigned' => 'The Service was successfully Assigned.',
          'warning1' => 'You can not update payment. Cos status is stil pending or processing.'
        ],
        'problem'=>[
          'updated'=> 'The Problem was successfully updated.',
          'created'=> 'The Problem was successfully created.',
          'deleted' => 'The Problem was successfully Removed.',
          'warning_delete' =>'The Problem could not Removed. It already do the Service transaction.',

        ],
        'category'=>[
          'updated'=> 'The Category was successfully updated.',
          'created'=> 'The Category was successfully created.',
          'deleted' => 'The Category was successfully Removed.',
          'warning_delete1' =>'The Category could not Removed. It already do the item transaction.',
          'warning_delete2' =>'The Category could not Removed. It already do the purchase transaction.',
        ],
        'saleorder'=>[
          'updated'=> 'The Sale Order was successfully updated.',
          'created'=> 'The Sale Order was successfully created.',
          'deleted' => 'The Sale Order was successfully Removed.',
          'warning_delete1' =>'The Sale Order could not Removed. It already do the sale transaction.',
        ],
        'purchase'=>[
          'updated'=> 'The RMA was successfully updated.',
          'created'=> 'The RMA was successfully created.',
          'deleted' => 'The RMA was successfully Removed.',
          'warning_delete1' =>'The RMA could not Removed. It already do the transaction.',
        ],
        'brand'=>[
          'updated'=> 'The Brand was successfully updated.',
          'created'=> 'The Brand was successfully created.',
          'deleted' => 'The Brand was successfully Removed.',
          'warning_delete1' =>'The Brand could not Removed. It already do the purchase transaction.',
          'warning_delete2' =>'The Brand could not Removed. It already do the sale transaction.',
        ],
        'rmacode'=>[
          'updated'=> 'The RMACode was successfully updated.',
          'created'=> 'The RMACode was successfully created.',
          'deleted' => 'The RMACode was successfully Removed.',
          'warning_delete1' =>'The RMACode could not Removed. It already do the service transaction.',
    //      'warning_delete2' =>'The RMACode could not Removed. It already do the sale transaction.',
        ],
        'item'=>[
          'updated'=> 'The Item was successfully updated.',
          'created'=> 'The Item was successfully created.',
          'deleted' => 'The Item was successfully Removed.',
          'warning_delete1' =>'The Item could not Removed. It already do the purchase transaction.',
          'warning_delete2' =>'The Item could not Removed. It already do the sale transaction.',
        ],
        'vendor'=>[
          'updated'=> 'The Vendor was successfully updated.',
          'created'=> 'The Vendor was successfully created.',
          'deleted' => 'The Vendor was successfully Removed.',
          'warning_delete1' =>'The Vendor could not Removed. It already do the service transaction.',
          'warning_delete2' =>'The Vendor could not Removed. It already do the purchase transaction.',
        ],
        'customer'=>[
          'updated'=> 'The Customer was successfully updated.',
          'created'=> 'The Customer was successfully created.',
          'deleted' => 'The Customer was successfully Removed.',
          'warning_delete' =>'The Customer could not Removed. It already do the Service transaction.',
        ],
        'users' => [
            'cant_resend_confirmation' => 'The application is currently set to manually approve users.',
            'confirmation_email' => 'A new confirmation e-mail has been sent to the address on file.',
            'confirmed' => 'The user was successfully confirmed.',
            'created' => 'The user was successfully created.',
            'deleted' => 'The user was successfully deleted.',
            'deleted_permanently' => 'The user was deleted permanently.',
            'restored' => 'The user was successfully restored.',
            'session_cleared' => "The user's session was successfully cleared.",
            'social_deleted' => 'Social Account Successfully Removed',
            'unconfirmed' => 'The user was successfully un-confirmed',
            'updated' => 'The user was successfully updated.',
            'updated_password' => "The user's password was successfully updated.",
        ],
    ],

    'frontend' => [
        'contact' => [
            'sent' => 'Your information was successfully sent. We will respond back to the e-mail provided as soon as we can.',
        ],
    ],
];
