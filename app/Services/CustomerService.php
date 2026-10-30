<?php

namespace App\Services;

use App\Interfaces\CustomerInterface;

class CustomerService
{
    public $customerInterface;
    /**
     * Create a new class instance.
     */
    public function __construct(CustomerInterface $customerInterface)
    {
        $this->customerInterface = $customerInterface;
    }

    /**
     * Summary of customerList
     * Function: customerList
     * Description: Fetch all customers
     * @return mixed
     */
    public function customerList(){
        return $this->customerInterface->getCustomers();
    }

    /**
     * Summary of getCustomer
     * Function: getCustomer
     * Description: get customer by id
     * @param int $id
     * @return mixed
     */
    public function getCustomer(int $id){
        return $this->customerInterface->getCustomer($id);
    }

    /**
     * Summary of updateCustomer
     * Function: updateCustomer
     * @param array $customer
     * @return mixed
     */
    public function updateCustomer(array $customer){
        return $this->customerInterface->updateCustomer($customer);
    }

    /**
     * Summary of deleteCustomer
     * Function: deleteCustomer
     * @param int $id
     * @return mixed
     */
    public function deleteCustomer(int $id){
        return $this->customerInterface->deleteCustomer($id);
    }

    /**
     * Summary of getCustomerForSelect
     * Function: getCustomerForSelect
     * Description: fetch customer data for select options
     * @return mixed
     */
    public function getCustomerForSelect(){
        return $this->customerInterface->getCustomerForSelect();
    }
}
