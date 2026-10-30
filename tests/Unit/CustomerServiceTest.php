<?php

use App\Interfaces\CustomerInterface;
use App\Repositories\CustomerRepository;
use App\Services\CustomerService;

test('Add new customer test', function(){
    $customerRepo = new CustomerRepository();
    $customer = [
        'id' => 0,
        'name' => 'My Name',
        'address' => 'My Address',
        'phone_prefix' => '+62',
        'phone' => '85217851070'
    ];
    expect($customerRepo->updateCustomer($customer))->toBe(true);

});
