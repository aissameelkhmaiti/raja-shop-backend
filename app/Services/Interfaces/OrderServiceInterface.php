<?php
namespace App\Services\Interfaces;

interface OrderServiceInterface
{
    public function getAllOrders();
    public function getOrderById($id);
    public function createOrder(array $data);
    public function updateOrder($id, array $data);
    public function deleteOrder($id);
}
