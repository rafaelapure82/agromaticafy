<?php

return [
    'product' => [
        'title' => 'Productos',
        'title_singular' => 'Producto',
        'fields' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'status' => 'Estado',
            'barcode' => 'Código de Barras',
            'image' => 'Imagen',
            'actions' => 'Acciones',
        ],
    ],
    'customer' => [
        'title' => 'Clientes',
        'title_singular' => 'Cliente',
        'fields' => [
            'id' => 'ID',
            'first_name' => 'Nombre',
            'last_name' => 'Apellido',
            'email' => 'Correo Electrónico',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'document_id' => 'Cédula/RIF',
            'actions' => 'Acciones',
        ],
    ],
    'order' => [
        'title' => 'Pedidos',
        'title_singular' => 'Pedido',
        'fields' => [
            'id' => 'ID',
            'customer' => 'Cliente',
            'total' => 'Total',
            'status' => 'Estado',
            'created_at' => 'Fecha',
            'actions' => 'Acciones',
        ],
    ],
];
