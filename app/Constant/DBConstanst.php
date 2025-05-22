<?php
namespace App\Constant;

class DBConstanst
{
    //order
    public const ORDER_STATUS_PENDING = 0;
    public const ORDER_STATUS_AR_APPROVED = 1;
    public const ORDER_STATUS_PAYMENT_APPROVED = 2;
    public const ORDER_STATUS_STOCK_AVAILABLE = 3;
    public const ORDER_STATUS_STOCK_UNAVAILABLE = 4;
    public const ORDER_STATUS_REQUESTED_TO_SUPPLIER = 5;
    public const ORDER_STATUS_SENT_TO_CUSTOMER = 6;
    public const ORDER_STATUS_DELIVERED = 7;
    public const ORDER_STATUS_REJECTED = 99;
    public const ORDER_STATUS_CANCELED = 100;
    public const ORDER_STATUS_INVOICED_PAYMENT_UNAPPROVED = 101;

    public const PAYMENT_METHOD_CASH = 0;
    public const PAYMENT_METHOD_AR = 1;
    public const PAYMENT_METHOD_TRANSFER = 2;
    public const PAYMENT_METHOD_CREDIT = 3;


}