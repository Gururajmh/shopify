<?php
/**
 * Created by PhpStorm.
 * @author Tareq Mahmood <tareqtms@yahoo.com>
 * Created at 8/16/16 10:42 AM UTC+06:00
 *
 * @see https://help.shopify.com/api/reference/ Shopify API Reference
 */

namespace PHPShopify;


/*
|--------------------------------------------------------------------------
| Shopify API SDK Client Class
|--------------------------------------------------------------------------
|
| This class initializes the resource objects
|
| Usage:
| //For private app
| $config = array(
|    'ShopUrl' => 'yourshop.myshopify.com',
|    'ApiKey' => '***YOUR-PRIVATE-API-KEY***',
|    'Password' => '***YOUR-PRIVATE-API-PASSWORD***',
| );
| //For third party app
| $config = array(
|    'ShopUrl' => 'yourshop.myshopify.com',
|    'AccessToken' => '***ACCESS-TOKEN-FOR-THIRD-PARTY-APP***',
| );
| //Create the shopify client object
| $shopify = new ShopifySDK($config);
|
| //Get shop details
| $products = $shopify->Shop->get();
|
| //Get list of all products
| $products = $shopify->Product->get();
|
| //Get a specific product by product ID
| $products = $shopify->Product($productID)->get();
|
| //Update a product
| $updateInfo = array('title' => 'New Product Title');
| $products = $shopify->Product($productID)->put($updateInfo);
|
| //Delete a product
| $products = $shopify->Product($productID)->delete();
|
| //Create a new product
| $productInfo = array(
|    "title" => "Burton Custom Freestlye 151",
|    "body_html" => "<strong>Good snowboard!<\/strong>",
|    "vendor" => "Burton",
|    "product_type" => "Snowboard",
| );
| $products = $shopify->Product->post($productInfo);
|
| //Get variants of a product (using Child resource)
| $products = $shopify->Product($productID)->Variant->get();
|
*/
use PHPShopify\Exception\SdkException;

class ShopifySDK
{
    /**
     * @var float microtime of last api call
     */
    public static $microtimeOfLastApiCall;

    /**
     * @var float Minimum gap in seconds to maintain between 2 api calls
     */
    public static $timeAllowedForEachApiCall = .5;

    /**
     * Shop / API configurations
     *
     * @var array
     */
    public static $config = array();

    /**
     * List of available resources which can be called from this client
     *
     * @var string[]
     */
    protected $resources = array(
        'AbandonedCheckout',
        'ApplicationCharge',
        'Blog',
        'CarrierService',
        'Collect',
        'Comment',
        'Country',
        'CustomCollection',
        'Customer',
        'CustomerSavedSearch',
        'Discount',
        'Event',
        'FulfillmentService',
        'GiftCard',
        'Location',
        'Metafield',
        'Multipass',
        'Order',
        'Page',
        'Policy',
        'Product',
        'ProductVariant',
        'RecurringApplicationCharge',
        'Redirect',
        'ScriptTag',
        'ShippingZone',
        'Shop',
        'SmartCollection',
        'Theme',
        'User',
        'Webhook',
    );

    /**
     * List of resources which are only available through a parent resource
     *
     * @var array Array key is the child resource name and array value is the parent resource name
     */
    protected $childResources = array(
        'Article'           => 'Blog',
        'Asset'             => 'Theme',
        'CustomerAddress'   => 'Customer',
        'Fulfillment'       => 'Order',
        'FulfillmentEvent'  => 'Fulfillment',
        'OrderRisk'         => 'Order',
        'ProductImage'      => 'Product',
        'ProductVariant'    => 'Product',
        'Province'          => 'Country',
        'Refund'            => 'Order',
        'Transaction'       => 'Order',
        'UsageCharge'       => 'RecurringApplicationCharge',
    );

    /*
     * ShopifySDK constructor
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct($config = array())
    {
        if(!empty($config)) {
            ShopifySDK::$config = $config;
            ShopifySDK::setAdminUrl();
        }
    }

    /**
     * Return ShopifyResource instance for a resource.
     * @example $shopify->Product->get(); //Returns all available Products
     * Called like an object properties (without parenthesis)
     *
     * @param string $resourceName
     *
     * @return ShopifyResource
     */
    public function __get($resourceName)
    {
        return $this->$resourceName();
    }

    /**
     * Return ShopifyResource instance for a resource.
     * Called like an object method (with parenthesis) optionally with the resource ID as the first argument
     * @example $shopify->Product($productID); //Return a specific product defined by $productID
     *
     * @param string $resourceName
     * @param array $arguments
     *
     * @throws SdkException if the $name is not a valid ShopifyResource resource.
     *
     * @return ShopifyResource
     */
    public function __call($resourceName, $arguments)
    {
        if (!in_array($resourceName, $this->resources)) {
            if (isset($this->childResources[$resourceName])) {
                $message = "$resourceName is a child resource of " . $this->childResources[$resourceName] . ". Cannot be accessed directly.";
            } else {
                $message = "Invalid resource name $resourceName. Pls check the API Reference to get the appropriate resource name.";
            }
            throw new SdkException($message);
        }

        $resourceClassName = __NAMESPACE__ . "\\$resourceName";

        //If first argument is provided, it will be considered as the ID of the resource.
        $resourceID = !empty($arguments) ? $arguments[0] : null;

        //Initiate the resource object
        $resource = new $resourceClassName($resourceID);

        return $resource;
    }

    /**
     * Configure the SDK client
     *
     * @param array $config
     *
     * @return ShopifySDK
     */
    public static function config($config)
    {
        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }

        //Re-set the admin url if shop url is changed
        if(isset($config['ShopUrl'])) {
            self::setAdminUrl();
        }

        //If want to keep more wait time than .5 seconds for each call
        if (isset($config['AllowedTimePerCall'])) {
            static::$timeAllowedForEachApiCall = $config['AllowedTimePerCall'];
        }

        return new ShopifySDK;
    }

    /**
     * Set the admin url, based on the configured shop url
     *
     * @return string
     */
    public static function setAdminUrl()
    {
        $shopUrl = self::$config['ShopUrl'];

        //Remove https:// and trailing slash (if provided)
        $shopUrl = preg_replace('#^https?://|/$#', '', $shopUrl);

        if(isset(self::$config['ApiKey']) && isset(self::$config['Password'])) {
            $apiKey = self::$config['ApiKey'];
            $apiPassword = self::$config['Password'];
            $adminUrl = "https://$apiKey:$apiPassword@$shopUrl/admin/";
        } else {
            $adminUrl = "https://$shopUrl/admin/";
        }

        self::$config['AdminUrl'] = $adminUrl;

        return $adminUrl;
    }

    /**
     * Get the admin url of the configured shop
     *
     * @return string
     */
    public static function getAdminUrl() {
        return self::$config['AdminUrl'];
    }

    /**
     * Maintain maximum 2 calls per second to the API
     *
     * @see https://help.shopify.com/api/guides/api-call-limit
     *
     * @param bool $firstCallWait Whether to maintain the wait time even if it is the first API call
     */
    public static function checkApiCallLimit($firstCallWait = false)
    {
        $timeToWait = 0;
        if (static::$microtimeOfLastApiCall == null) {
            if ($firstCallWait) {
                $timeToWait = static::$timeAllowedForEachApiCall;
            }
        } else {
            $now = microtime(true);
            $timeSinceLastCall = $now - static::$microtimeOfLastApiCall;
            //Ensure 2 API calls per second
            if($timeSinceLastCall < static::$timeAllowedForEachApiCall) {
                $timeToWait = static::$timeAllowedForEachApiCall - $timeSinceLastCall;
            }
        }

        if ($timeToWait) {
            //convert time to microseconds
            $microSecondsToWait = $timeToWait * 1000000;
            //Wait to maintain the API call difference of .5 seconds
            usleep($microSecondsToWait);
        }

        static::$microtimeOfLastApiCall = microtime(true);
    }
}