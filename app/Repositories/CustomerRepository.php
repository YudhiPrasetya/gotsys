<?php

namespace App\Repositories;

use App\Interfaces\CustomerInterface;
use App\Models\Customer;

class CustomerRepository implements CustomerInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getCustomers(){
        return Customer::selectRaw("id, name, address, CONCAT(prefix_phone,'',phone) as phone")->orderBy('id', 'desc')->paginate(5)->withQueryString();
    }
    public function getCustomer(int $id){
        return Customer::find($id);
    }
    // public function createCustomer(array $newCustomer){
    //     return Customer::create($newCustomer);
    // }
    public function updateCustomer(array $customer){
        // return Customer::update($customer);
        return Customer::updateOrInsert(['id' => $customer['id']], function($exists) use($customer){
            if($exists){
                return [
                    'name' => $customer['name'],
                    'address' => $customer['address'],
                    'prefix_phone' => $customer['prefix_phone'],
                    'phone' => $customer['phone']
                ];
            }
            return [
                'name' => $customer['name'],
                'address' => $customer['address'],
                'prefix_phone' => $customer['prefix_phone'],
                'phone' => $customer['phone']
            ];
        });
    }
    public function deleteCustomer(int $id){
        // return Customer::delete($id);
        return Customer::find($id)?->delete();
    }

    public function getCustomerForSelect(){
        return Customer::select(['id', 'name'])->get();
    }
}
