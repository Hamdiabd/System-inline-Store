<?php
class Order extends Model {
    protected $table = 'orders';
    
    /**
     * إنشاء طلب جديد
     */
    public function createOrder($userId, $cartItems, $addressId, $shippingMethod, $paymentMethod) {
        try {
            $this->db->beginTransaction();
            
            // حساب المجاميع
            $subtotal = 0;
            foreach($cartItems as $item) {
                $subtotal += $item->price * $item->quantity;
            }
            
            $tax = $subtotal * 0.15; // 15% ضريبة
            $shippingCost = ($shippingMethod == 'express') ? 25 : 0;
            $total = $subtotal + $tax + $shippingCost;
            
            // إنشاء رقم طلب فريد
            $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
            
            // جلب عنوان الشحن
            $this->db->query("SELECT * FROM addresses WHERE id = :address_id");
            $this->db->bind(':address_id', $addressId);
            $address = $this->db->single();
            $shippingAddress = $address->address . ', ' . $address->city;
            
            // إدخال الطلب
            $this->db->query("INSERT INTO {$this->table} 
                              (user_id, order_number, subtotal, tax, shipping_cost, total, status, payment_status) 
                              VALUES 
                              (:user_id, :order_number, :subtotal, :tax, :shipping_cost, :total, 'pending', 'unpaid')");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':order_number', $orderNumber);
            $this->db->bind(':subtotal', $subtotal);
            $this->db->bind(':tax', $tax);
            $this->db->bind(':shipping_cost', $shippingCost);
            $this->db->bind(':total', $total);
            
            if($this->db->execute()) {
                $orderId = $this->db->lastInsertId();
                
                // إدخال عناصر الطلب
                foreach($cartItems as $item) {
                    $this->db->query("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                                      VALUES (:order_id, :product_id, :quantity, :price, :subtotal)");
                    $this->db->bind(':order_id', $orderId);
                    $this->db->bind(':product_id', $item->product_id);
                    $this->db->bind(':quantity', $item->quantity);
                    $this->db->bind(':price', $item->price);
                    $this->db->bind(':subtotal', $item->price * $item->quantity);
                    $this->db->execute();
                    
                    // تحديث المخزون
                    $this->db->query("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
                    $this->db->bind(':quantity', $item->quantity);
                    $this->db->bind(':product_id', $item->product_id);
                    $this->db->execute();
                }
                
                // إدخال معلومات الشحن
                $this->db->query("INSERT INTO shipments (order_id, shipping_address, status) 
                                  VALUES (:order_id, :shipping_address, 'pending')");
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':shipping_address', $shippingAddress);
                $this->db->execute();
                
                // إدخال معلومات الدفع
                $this->db->query("INSERT INTO payments (order_id, amount, method, status) 
                                  VALUES (:order_id, :amount, :method, 'pending')");
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':amount', $total);
                $this->db->bind(':method', $paymentMethod);
                $this->db->execute();
                
                $this->db->commit();
                return $orderId;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * جلب طلبات مستخدم
     */
    public function getUserOrders($userId) {
        $this->db->query("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    /**
     * جلب تفاصيل طلب مع المنتجات
     */
    public function getOrderDetails($orderId) {
        $this->db->query("SELECT o.*, u.name as user_name, u.email, u.phone,
                          s.tracking_number, s.carrier, s.status as shipment_status,
                          p.method as payment_method, p.status as payment_status
                          FROM {$this->table} o
                          JOIN users u ON u.id = o.user_id
                          LEFT JOIN shipments s ON s.order_id = o.id
                          LEFT JOIN payments p ON p.order_id = o.id
                          WHERE o.id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->single();
    }
    
    /**
     * جلب منتجات الطلب
     */
    public function getOrderItems($orderId) {
        $this->db->query("SELECT oi.*, p.name, p.main_image
                          FROM order_items oi
                          JOIN products p ON p.id = oi.product_id
                          WHERE oi.order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }
    
    /**
     * تحديث حالة الطلب
     */
    public function updateStatus($orderId, $status) {
        $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $orderId);
        return $this->db->execute();
    }
}