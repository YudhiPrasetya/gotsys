<?php

namespace App\Interfaces;

interface CustomerInterface
{
    public function getCustomers();
    public function getCustomer(int $id);
    // public function createCustomer(array $newCustomer);
    public function updateCustomer(array $customer);
    public function deleteCustomer(int $id);

    public function getCustomerForSelect();

}
