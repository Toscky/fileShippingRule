<?php

/**

 * 2007-2022 ETS-Soft

 *

 * NOTICE OF LICENSE

 *

 * This file is not open source! Each license that you purchased is only available for 1 wesite only.

 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.

 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.

 *

 * DISCLAIMER

 *

 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer

 * versions in the future. If you wish to customize PrestaShop for your

 * needs please contact us for extra customization service at an affordable price

 *

 *  @author ETS-Soft <etssoft.jsc@gmail.com>

 *  @copyright  2007-2022 ETS-Soft

 *  @license    Valid for 1 website (or project) for each purchase of license

 *  International Registered Trademark & Property of ETS-Soft

 */



if (!defined('_PS_VERSION_'))

	exit;

class Ets_sc_shipping_rule extends Ets_sc_obj

{

    public static $instance;

    public $name;

    public $description;

    public $priority;

    public $id_shop;

    public $active;

    public $id_carriers;

    public $new_customer;

    public $type_combine_condition ='and';

    public $date_add;

    public $date_upd;

    public static $definition = array(

		'table' => 'ets_sc_shipping_rule',

		'primary' => 'id_ets_sc_shipping_rule',

		'multilang' => true,

		'fields' => array(

            'active' => array('type' => self::TYPE_INT),

            'priority' => array('type' => self::TYPE_INT),

            'id_shop' => array('type' => self::TYPE_INT),

            'new_customer' => array('type' => self::TYPE_INT),

            'id_carriers' => array('type'=> self::TYPE_STRING),

            'type_combine_condition' => array('type'=>self::TYPE_STRING),

            'date_add' => array('type' => self::TYPE_DATE),

            'date_upd' => array('type' => self::TYPE_DATE),

            'name' => array('type' => self::TYPE_STRING,'lang'=>true),

			'description' => array('type' => self::TYPE_STRING,'lang'=>true),



        )

	);

    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)

	{

		parent::__construct($id_item, $id_lang, $id_shop);

	}

    public static function getInstance()

    {

        if (!(isset(self::$instance)) || !self::$instance) {

            self::$instance = new Ets_sc_shipping_rule();

        }

        return self::$instance;

    }

    public function l($string,$file_name='')

    {

        return Translate::getModuleTranslation('ets_promotion', $string, $file_name ? : pathinfo(__FILE__, PATHINFO_FILENAME));

    }

    public function getListFields()

    {

        return array(

            'form' => array(

                'legend' => array(

                    'title' => $this->l('Information') ,

                ),

                'input' => array(),

                'submit' => array(

                    'title' => $this->l('Save & Next'),

                    'icon' => 'icon-circle-arrow-right process-icon-large',

                ),

                'buttons'=> array(

                    array(

                        'title' => $this->l('Save & Stay'),

                        'type' => 'submit',

                        'class' => 'pull-right',

                        'name' => 'btnSubmitShippingRuleStay',

                        'icon' => 'process-icon-save',

                    ),

                    array(

                        'title' => $this->l('Cancel'),

                        'type' => 'submit',

                        'class' => 'pull-left',

                        'name' => 'btncancel',

                        'href' => Context::getContext()->link->getAdminLink('AdminShippingCostRule'),

                        'icon' => 'process-icon-cancel',

                    )

                ),

                'name' => 'shipping_rule',

                'key' => 'id_ets_sc_shipping_rule',

            ),

            'configs' => array(

                'active'=>array(

                    'type'=>'switch',

                    'label'=>$this->l('Active'),

                    'default' => 1,

                    'values' => array(

                        array(

                            'label' => $this->l('Yes'),

                            'id' => 'active_on',

                            'value' => 1,

                        ),

                        array(

                            'label' => $this->l('No'),

                            'id' => 'active_off',

                            'value' => 0,

                        )

                    ),

                    'tab' => 'information'

                ),

                'name'=>array(

                    'type'=>'text',

                    'label'=>$this->l('Shipping rule'),

                    'lang' => true,

                    'tab' => 'information',

                    'validate' => 'isCleanHtml',

                    'required' => true,

                ),

                'description'=>array(

                    'type'=>'textarea',

                    'label'=>$this->l('Rule description'),

                    'lang' => true,

                    'tab' => 'information',

                    'validate' => 'isCleanHtml',

                ),

                'priority' => array(

                    'type'=>'text',

                    'label'=>$this->l('Priority'),

                    'desc' =>$this->l('Shipping rules are applied by priority. A rule with a priority of "1" will be processed before a rule with priority of "2"'),

                    'tab' => 'information',

                    'col' => 6,

                    'validate' => 'isUnsignedInt',

                    'default' => 1,

                ),

                'id_carriers' => array(

                    'type' => 'checkbox',

                    'values' => array(

                        'query' => $this->getCarriers(),

                        'id' => 'id_reference',

                        'name' => 'name'

                    ),

                    'label' => $this->l('Apply to carriers'),

                    'tab' => 'information',

                    'default' => array('all'),

                    'validate' => 'isCleanHtml',

                    'showRequired' => true,

                ),

            ),

        );

    }

    public static function getShippingRules($filter='',$sort='',$start=0,$limit=10,$total=false)

    {

        $id_lang = (int)Context::getContext()->language->id;

        $id_shop = (int)Context::getContext()->shop->id;

        if($total)

            $sql ='SELECT COUNT(DISTINCT sr.id_ets_sc_shipping_rule) FROM`'._DB_PREFIX_.'ets_sc_shipping_rule`sr';

        else

            $sql ='SELECT sr.*,srl.name,srl.description,ar.type_action FROM `'._DB_PREFIX_.'ets_sc_shipping_rule` sr';

        $sql .=' LEFT JOIN `'._DB_PREFIX_.'ets_sc_shipping_rule_lang` srl ON (sr.id_ets_sc_shipping_rule=srl.id_ets_sc_shipping_rule AND srl.id_lang="'.(int)$id_lang.'")

        LEFT JOIN `'._DB_PREFIX_.'ets_sc_action_rule` ar ON (ar.id_ets_sc_shipping_rule = sr.id_ets_sc_shipping_rule)

        WHERE sr.id_shop= "'.(int)$id_shop.'" '.($filter ? $filter: '');

        if($total)

            return Db::getInstance()->getValue($sql);

        else

        {

            $sql .=($sort ? ' ORDER BY '.$sort: ' ORDER BY sr.id_ets_sc_shipping_rule asc').' LIMIT '.(int)$start.','.(int)$limit.'';

            return Db::getInstance()->executeS($sql);

        }

    }

    public function delete()

    {

        if(parent::delete())

        {

            Db::getInstance()->execute('DELETE FROM`'._DB_PREFIX_.'ets_sc_condition_rule`WHERE id_ets_sc_shipping_rule='.(int)$this->id);

            return true;

        }

    }

    public static function getShippingRulesActive()

    {

        $id_shop = (int)Context::getContext()->shop->id;

        $sql = 'SELECT *,if(ar.type_action="increase",4,if(ar.type_action="replace",3,if(ar.type_action="decrease",2,1))) as action FROM `'._DB_PREFIX_.'ets_sc_shipping_rule` sr

        INNER JOIN `'._DB_PREFIX_.'ets_sc_action_rule` ar ON (sr.id_ets_sc_shipping_rule = ar.id_ets_sc_shipping_rule)

        WHERE sr.id_shop="'.(int)$id_shop.'" AND sr.active=1 ORDER BY priority ASC,action DESC';

        return Db::getInstance()->executeS($sql);

    }

    public function checkSpecificCustomer($condition)

    {

        $context = Context::getContext();

        if($condition['id_customers'])

        {

            $id_customers = explode(',',$condition['id_customers']);

            if($context->customer->id && in_array($context->customer->id,$id_customers))

                return true;

        }

        return false;

    }

    public function checkGroupCustomer($condition)

    {

        if($condition['id_groups']=='all')

            return true;

        elseif($condition['id_groups'])

        {

            $context = Context::getContext();

            $id_groups = explode(',',$condition['id_groups']);

            $groups = '';

            foreach($id_groups as $id)

            {

                $group = new Group($id,$context->language->id);

                $groups .=$group->name.', ';

            }

            if ($context->customer->id) {

                if($condition['only_apply_on_default_group'])

                {

                    $id_group = Customer::getDefaultGroupId((int)$context->customer->id);

                    if(in_array($id_group,$id_groups))

                        return true;

                    else

                        return false;

                }

                else

                {

                    $customer_groups = Db::getInstance()->executeS('SELECT id_group FROM`'._DB_PREFIX_.'customer_group`WHERE id_customer='.(int)$context->customer->id);

                    if($customer_groups)

                    {

                        foreach($customer_groups as $customer_group)

                        {

                            if(in_array($customer_group['id_group'],$id_groups))

                                return true;

                        }

                    }

                    return false;

                }



            }

            else{

                $id_group = (int)Group::getCurrent()->id;

                if(in_array($id_group,$id_groups))

                    return  true;

                else

                    return false;

            }

        }

    }

    public function checkMemberShipCustomer($condition)

    {

        if( ($condition['customer_signed_up_from']&& $condition['customer_signed_up_from']!='0000-00-00 00:00:00') || ($condition['customer_signed_up_to']!='0000-00-00 00:00:00' && $condition['customer_signed_up_to']) || $condition['days_since_singed_up_day'])

        {

            $customer = Context::getContext()->customer;

            if($customer->id)

            {

                $check_customer_signed_up  = true;

                if($condition['customer_signed_up_from'] && $condition['customer_signed_up_from']!='0000-00-00 00:00:00' && (strtotime($customer->date_add) < strtotime($condition['customer_signed_up_from']) ) )

                    $check_customer_signed_up = false;

                if($condition['customer_signed_up_to'] && $condition['customer_signed_up_to']!='0000-00-00 00:00:00' && (strtotime($customer->date_add) > strtotime($condition['customer_signed_up_to'])) )

                    $check_customer_signed_up = false;

                if(!$check_customer_signed_up)

                    return false;

                if($condition['days_since_singed_up_day'])

                {

                    $days = (int)(strtotime(date('Y-m-d H:i:s')) - strtotime($customer->date_add))/86400;

                    if($condition['days_since_signed_up_cal']=='>=' && ((int)$days < $condition['days_since_singed_up_day']) )

                        return false;

                    if($condition['days_since_signed_up_cal']=='=' && ((int)$days != $condition['days_since_singed_up_day']))

                        return false;

                    if($condition['days_since_signed_up_cal']=='<=' && ((int)$days > $condition['days_since_singed_up_day']))

                        return false;

                }

            }

            else

                return false;

        }

        return true;

    }

    public function getDeliveryOptionList(Country $default_country = null,$flush= false)

    {

        $cart = Context::getContext()->cart;

        $delivery_option_list = [];

        $carriers_price = [];

        $carrier_collection = [];

        $package_list = $cart->getPackageList($flush);



        // Foreach addresses

        foreach ($package_list as $id_address => $packages) {

            // Initialize vars

            $delivery_option_list[$id_address] = [];

            $carriers_price[$id_address] = [];

            $common_carriers = null;

            $best_price_carriers = [];

            $best_grade_carriers = [];

            $carriers_instance = [];



            // Get country

            if ($id_address) {

                $address = new Address($id_address);

                $country = new Country($address->id_country);

            } else {

                $country = $default_country;

            }



            // Foreach packages, get the carriers with best price, best position and best grade

            foreach ($packages as $id_package => $package) {

                // No carriers available

                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {

                    return array();

                }



                $carriers_price[$id_address][$id_package] = [];



                // Get all common carriers for each packages to the same address

                if (null === $common_carriers) {

                    $common_carriers = $package['carrier_list'];

                } else {

                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);

                }



                $best_price = null;

                $best_price_carrier = null;

                $best_grade = null;

                $best_grade_carrier = null;



                // Foreach carriers of the package, calculate his price, check if it the best price, position and grade

                foreach ($package['carrier_list'] as $id_carrier) {

                    if (!isset($carriers_instance[$id_carrier])) {

                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);

                    }



                    $price_with_tax = $cart->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list'],null,false,true);

                    $price_without_tax = $cart->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list'],null,false,true);

                    if (null === $best_price || $price_with_tax < $best_price) {

                        $best_price = $price_with_tax;

                        $best_price_carrier = $id_carrier;

                    }

                    $carriers_price[$id_address][$id_package][$id_carrier] = [

                        'without_tax' => $price_without_tax,

                        'with_tax' => $price_with_tax,

                    ];



                    $grade = $carriers_instance[$id_carrier]->grade;

                    if (null === $best_grade || $grade > $best_grade) {

                        $best_grade = $grade;

                        $best_grade_carrier = $id_carrier;

                    }



                    $best_grade_carrier = $id_carrier;



                }



                $best_price_carriers[$id_package] = $best_price_carrier;

                $best_grade_carriers[$id_package] = $best_grade_carrier;

            }



            // Reset $best_price_carrier, it's now an array

            $best_price_carrier = [];

            $key = '';



            // Get the delivery option with the lower price

            foreach ($best_price_carriers as $id_package => $id_carrier) {

                $key .= $id_carrier . ',';

                if (!isset($best_price_carrier[$id_carrier])) {

                    $best_price_carrier[$id_carrier] = [

                        'price_with_tax' => 0,

                        'price_without_tax' => 0,

                        'package_list' => [],

                        'product_list' => [],

                    ];

                }

                $best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];

                $best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];

                $best_price_carrier[$id_carrier]['package_list'][] = $id_package;

                $best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);

                $best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];

                $real_best_price = !isset($real_best_price) || $real_best_price > $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] ?

                    $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] : $real_best_price;

                $real_best_price_wt = !isset($real_best_price_wt) || $real_best_price_wt > $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] ?

                    $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] : $real_best_price_wt;

            }



            // Add the delivery option with best price as best price

            $delivery_option_list[$id_address][$key] = [

                'carrier_list' => $best_price_carrier,

                'is_best_price' => true,

                'is_best_grade' => false,

                'unique_carrier' => (count($best_price_carrier) <= 1),

            ];



            // Reset $best_grade_carrier, it's now an array

            $best_grade_carrier = [];

            $key = '';



            // Get the delivery option with the best grade

            foreach ($best_grade_carriers as $id_package => $id_carrier) {

                $key .= $id_carrier . ',';

                if (!isset($best_grade_carrier[$id_carrier])) {

                    $best_grade_carrier[$id_carrier] = [

                        'price_with_tax' => 0,

                        'price_without_tax' => 0,

                        'package_list' => [],

                        'product_list' => [],

                    ];

                }

                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];

                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];

                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;

                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);

                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];

            }



            // Add the delivery option with best grade as best grade

            if (!isset($delivery_option_list[$id_address][$key])) {

                $delivery_option_list[$id_address][$key] = [

                    'carrier_list' => $best_grade_carrier,

                    'is_best_price' => false,

                    'unique_carrier' => (count($best_grade_carrier) <= 1),

                ];

            }

            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;



            // Get all delivery options with a unique carrier

            foreach ($common_carriers as $id_carrier) {

                $key = '';

                $package_list = [];

                $product_list = [];

                $price_with_tax = 0;

                $price_without_tax = 0;



                foreach ($packages as $id_package => $package) {

                    $key .= $id_carrier . ',';

                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];

                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];

                    $package_list[] = $id_package;

                    $product_list = array_merge($product_list, $package['product_list']);

                }



                if (!isset($delivery_option_list[$id_address][$key])) {

                    $delivery_option_list[$id_address][$key] = [

                        'is_best_price' => false,

                        'is_best_grade' => false,

                        'unique_carrier' => true,

                        'carrier_list' => [

                            $id_carrier => [

                                'price_with_tax' => $price_with_tax,

                                'price_without_tax' => $price_without_tax,

                                'instance' => $carriers_instance[$id_carrier],

                                'package_list' => $package_list,

                                'product_list' => $product_list,

                            ],

                        ],

                    ];

                } else {

                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);

                }

            }

        }



        $cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $cart, true);



        $result = false;

        if ($cart->id) {

            $result = Db::getInstance()->executeS('SELECT * FROM`' . _DB_PREFIX_ . 'cart_cart_rule`WHERE id_cart = ' . (int) $cart->id);

        }



        $cart_rules_in_cart = [];



        if (is_array($result)) {

            foreach ($result as $row) {

                $cart_rules_in_cart[] = $row['id_cart_rule'];

            }

        }



        $total_products_wt = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        $total_products = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);



        $free_carriers_rules = [];



        $context = Context::getContext();

        foreach ($cart_rules as $cart_rule) {

            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;

            $total_price += $cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price : 0;

            $total_price += !$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price_wt : 0;

            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']

                && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart)

                && $cart_rule['minimum_amount'] <= $total_price) {

                $cr = new CartRule((int) $cart_rule['id_cart_rule']);

                if (Validate::isLoadedObject($cr) &&

                    $cr->checkValidity($context, in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {

                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);

                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {

                        foreach ($carriers['selected'] as $carrier) {

                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {

                                $free_carriers_rules[] = (int) $carrier['id_carrier'];

                            }

                        }

                    }

                }

            }

        }



        // For each delivery options :

        //    - Set the carrier list

        //    - Calculate the price

        //    - Calculate the average position

        foreach ($delivery_option_list as $id_address => $delivery_option) {

            foreach ($delivery_option as $key => $value) {

                $total_price_with_tax = 0;

                $total_price_without_tax = 0;

                $position = 0;

                foreach ($value['carrier_list'] as $id_carrier => $data) {

                    $total_price_with_tax += $data['price_with_tax'];

                    $total_price_without_tax += $data['price_without_tax'];

                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;



                    if (!isset($carrier_collection[$id_carrier])) {

                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);

                    }

                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];



                    if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {

                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';

                    } else {

                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;

                    }



                    $position += $carrier_collection[$id_carrier]->position;

                }

                $delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;

                $delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;

                $delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;

                $delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);

            }

        }



        // Sort delivery option list

        foreach ($delivery_option_list as &$array) {

            uasort($array, ['Cart', 'sortDeliveryOptionList']);

        }

        return $delivery_option_list;

    }

    public function getDeliveryOption($default_country = null, $dontAutoSelectOptions = false)

    {

        $cart = Context::getContext()->cart;

        $delivery_option_list = $this->getDeliveryOptionList($default_country);



        // The delivery option was selected

        if (isset($cart->delivery_option) && $cart->delivery_option != '') {

            $delivery_option = json_decode($cart->delivery_option, true);

            $validated = true;



            if (is_array($delivery_option)) {

                foreach ($delivery_option as $id_address => $key) {

                    if (!isset($delivery_option_list[$id_address][$key])) {

                        $validated = false;



                        break;

                    }

                }



                if ($validated) {

                    return $delivery_option;

                }

            }

        }



        if ($dontAutoSelectOptions) {

            return false;

        }



        // No delivery option selected or delivery option selected is not valid, get the better for all options

        $delivery_option = [];

        foreach ($delivery_option_list as $id_address => $options) {

            foreach ($options as $key => $option) {

                if (Configuration::get('PS_CARRIER_DEFAULT') == -1 && $option['is_best_price']) {

                    $delivery_option[$id_address] = $key;



                    break;

                } elseif (Configuration::get('PS_CARRIER_DEFAULT') == -2 && $option['is_best_grade']) {

                    $delivery_option[$id_address] = $key;



                    break;

                } elseif ($option['unique_carrier'] && in_array(Configuration::get('PS_CARRIER_DEFAULT'), array_keys($option['carrier_list']))) {

                    $delivery_option[$id_address] = $key;



                    break;

                }

            }



            reset($options);

            if (!isset($delivery_option[$id_address])) {

                $delivery_option[$id_address] = key($options);

            }

        }



        return $delivery_option;

    }

    public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)

    {

        if (isset(Context::getContext()->cookie->id_country)) {

            $default_country = new Country(Context::getContext()->cookie->id_country);

        }

        if (null === $delivery_option) {

            $delivery_option = $this->getDeliveryOption($default_country, false, false);

        }



        $_total_shipping = [

            'with_tax' => 0,

            'without_tax' => 0,

        ];

        $delivery_option_list = $this->getDeliveryOptionList($default_country);

        foreach ($delivery_option as $id_address => $key) {

            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key])) {

                continue;

            }



            $_total_shipping['with_tax'] += $delivery_option_list[$id_address][$key]['total_price_with_tax'];

            $_total_shipping['without_tax'] += $delivery_option_list[$id_address][$key]['total_price_without_tax'];

        }



        return ($use_tax) ? $_total_shipping['with_tax'] : $_total_shipping['without_tax'];

    }

    public static function checkCalculator($source,$value,$cal)

    {

        if($cal=='>=' && $value < $source)

            return false;

        if($cal=='=' && $value != $source)

            return false;

        if($cal=='<=' && $value > $source)

            return false;

        return true;

    }

    public function checkCartAmount($condition,$id_carrier)

    {

        $module = Module::getInstanceByName('ets_shippingcost');

        if($module->check_shipping_discount)

            return true;

        $cart_amount_cal = $condition['cart_amount_cal'];

        $cart_amount = Tools::convertPrice((float)$condition['cart_amount']);

        $cart = Context::getContext()->cart;

        $withTaxes = $condition['cart_amount_tax_incl'] ? true: false;

        $totalCart = $cart->getOrderTotal($withTaxes,Cart::ONLY_PRODUCTS,null,null,false,false,false);

        if($condition['cart_amount_shipping_incl'])

            $totalCart += $cart->getPackageShippingCost($id_carrier,true,null,null,null,false,true);

        if($condition['cart_amount_discount_incl'])

        {



            $module->check_shipping_discount = true;

            $totalCart -= $cart->getOrderTotal($withTaxes,Cart::ONLY_DISCOUNTS,null,null,false,false,false);

            $module->check_shipping_discount = false;

        }

        if(!self::checkCalculator($cart_amount,$totalCart,$cart_amount_cal))

            return false;

        return true;

    }

    public function checkProductInCart($condition,$id_product = false)

    {

        $cart = Context::getContext()->cart;

        $products = $cart->getProducts();

        if(!$products)

            return false;

        if($cart->id)

        {

            if(!$id_product && (float)$condition['total_weight']<=0 && !(int)$condition['total_product_quantity'] && !(int)$condition['quantity_of_same_product'] && !(int)$condition['number_of_different_product'] && !(int)$condition['number_of_product_in_same_category'])

                return true;

            $product_includes = array();

            $attribute_includes = array();

            $product_excludes = array();

            $attribute_excludes = array();

            $products_with_different_attribute = (int)$condition['products_with_different_attribute'];

            if(!$condition['apply_for_discounted_products'])

            {

                if($products)

                {

                    foreach($products as $product)

                    {

                        if(isset($product['specific_prices']) && $product['specific_prices'])

                        {

                            if($product['id_product_attribute'] && $products_with_different_attribute)

                                $attribute_excludes[] = $product['id_product_attribute'];

                            else

                                $product_excludes[] = $product['id_product'];

                        }

                    }

                }

            }

            if($condition['exclude_products'])

            {

                $exclude_products = explode(',',$condition['exclude_products']);

                if($exclude_products)

                {

                    foreach($exclude_products as $exdlude_product)

                    {

                        $ids = explode('-',$exdlude_product);

                        if(isset($ids[1]) && $ids[1] && $products_with_different_attribute)

                            $attribute_excludes[] = $ids[1];

                        elseif($ids[0])

                            $product_excludes[] = $ids[0];

                    }

                }



            }

            $filter = '';

            $leftJoin ='';

            $joinProduct = false;

            $include_check=false;

            if($condition['applicable_product_categories']=='specific_product')

            {

                if($condition['applicable_categories'])

                {

                    $include_check = true;

                    $id_categories = explode(',',$condition['applicable_categories']);

                    if(!$condition['include_sub_categories'])

                    {

                        $sql = 'SELECT cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp 

                        INNER JOIN `'._DB_PREFIX_.'category_product` cap ON (cap.id_product = cp.id_product)

                        WHERE cp.id_cart="'.(int)Context::getContext()->cart->id.'" AND cap.id_category IN ('.implode(',',array_map('intval',$id_categories)).')

                        GROUP BY cp.id_product,cp.id_product_attribute';

                    }

                    else

                    {

                        $sql = 'SELECT cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp 

                        INNER JOIN `'._DB_PREFIX_.'category_product` cap ON (cap.id_product = cp.id_product)

                        LEFT JOIN `'._DB_PREFIX_.'category` ca ON (ca.id_category = cap.id_category)

                        LEFT JOIN `'._DB_PREFIX_.'category` ca2 ON (ca.id_parent = ca2.id_category)

                        WHERE cp.id_cart="'.(int)Context::getContext()->cart->id.'" AND (cap.id_category IN ('.implode(',',array_map('intval',$id_categories)).') OR ca2.id_category IN ('.implode(',',array_map('intval',$id_categories)).') )

                        GROUP BY cp.id_product,cp.id_product_attribute';

                    }

                    $cart_products = Db::getInstance()->executeS($sql);

                    if($cart_products)

                    {

                        foreach($cart_products as $cart_product)

                        {

                            if($products_with_different_attribute && $cart_product['id_product_attribute'])

                                $attribute_includes[] = $cart_product['id_product_attribute'];

                            else

                                $product_includes[] = $cart_product['id_product'];

                        }

                    }

                }

                if($condition['include_specific_products'])

                {

                    $include_check = true;

                    $include_products = explode(',',$condition['include_specific_products']);

                    if($include_products)

                    {

                        foreach($include_products as $include_product)

                        {

                            $ids = explode('-',$include_product);

                            if(isset($ids[1]) && $ids[1] && $products_with_different_attribute)

                                $attribute_includes[] = $ids[1];

                            elseif($ids[0])

                                $product_includes[] = $ids[0];

                        }

                    }

                }

            }

            if($condition['apply_for_product_price']!=0)

            {

                if(!$products_with_different_attribute)

                {

                    if(!$joinProduct)

                    {

                        $leftJoin .=' LEFT JOIN  `'._DB_PREFIX_.'product` p ON (p.id_product = cp.id_product)';

                        $joinProduct = true;

                    }

                    $filter .= ' AND p.price '.pSQL($condition['apply_for_product_price_cal'],true).'"'.(float)$condition['apply_for_product_price'].'"';

                }

                else

                {

                    $apply_for_product_price = Tools::convertPrice($condition['apply_for_product_price']);

                    foreach($products as $product)

                    {

                        $price = Product::getPriceStatic($product['id_product'],true,$product['id_product_attribute']);

                        if(!self::checkCalculator($apply_for_product_price,$price,$condition['apply_for_product_price_cal']))

                        {

                            if($product['id_product_attribute'])

                                $attribute_excludes[] = $product['id_product_attribute'];

                            else

                                $product_excludes[] = $product['id_product'];

                        }

                    }

                }

            }

            if($condition['apply_for_availabled_quantity_stock']!=0)

            {

                if(!$products_with_different_attribute)

                {

                    if(!$joinProduct)

                    {

                        $leftJoin .=' LEFT JOIN  `'._DB_PREFIX_.'product` p ON (p.id_product = cp.id_product)';

                        $joinProduct = true;

                    }

                    $leftJoin .=' LEFT JOIN `'._DB_PREFIX_.'stock_available` stock ON (p.id_product = stock.id_product AND stock.id_product_attribute=0)';

                    $filter .= ' AND stock.quantity '.pSQL($condition['apply_for_availabled_quantity_stock_cal'],true).'"'.(int)$condition['apply_for_availabled_quantity_stock'].'"';

                }

                else

                {

                    $apply_for_availabled_quantity_stock = $condition['apply_for_availabled_quantity_stock'];

                    foreach($products as $product)

                    {

                        $quantity = StockAvailable::getQuantityAvailableByProduct($product['id_product'],$product['id_product_attribute']);

                        if(!self::checkCalculator($apply_for_availabled_quantity_stock,$quantity,$condition['apply_for_availabled_quantity_stock_cal']))

                        {

                            if($product['id_product_attribute'])

                                $attribute_excludes[] = $product['id_product_attribute'];

                            else

                                $product_excludes[] = $product['id_product'];

                        }

                    }

                }

            }

            $ok = true;

            if(!$condition['apply_all_attribute'] && $condition['select_attributes'] && $condition['select_attributes']!='all')

            {

                $include_check = true;

                $select_attributes = explode(',',$condition['select_attributes']);

                $sql = 'SELECT DISTINCT cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp

                LEFT JOIN  `'._DB_PREFIX_.'product_attribute_combination` a ON (a.id_product_attribute = cp.id_product_attribute)

                WHERE cp.id_cart = "'.(int)$cart->id.'"  AND a.id_attribute IN ('.implode(',',array_map('intval',$select_attributes)).')';

                if($attributes = Db::getInstance()->executeS($sql))

                {

                    foreach($attributes as $attibute)

                    {

                        if($products_with_different_attribute && $attibute['id_product_attribute'])

                            $attribute_includes[] = $attibute['id_product_attribute'];

                        else

                            $product_includes[] = $attibute['id_product'];

                    }

                }

                else

                   $ok = false;//  return false;

            }

            if($ok && !$condition['apply_all_features'] && $condition['select_features'] && $condition['select_features']!='all')

            {

                $include_check = true;

                $select_features = explode(',',$condition['select_features']);

                $sql = 'SELECT DISTINCT cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp

                LEFT JOIN `'._DB_PREFIX_.'feature_product` fp ON (fp.id_product = cp.id_product)

                LEFT JOIN `'._DB_PREFIX_.'feature` f ON (f.id_feature = fp.id_feature)

                WHERE cp.id_cart="'.(int)$cart->id.'" AND f.id_feature IN ('.implode(',',array_map('intval',$select_features)).')';

                if($features = Db::getInstance()->executeS($sql))

                {

                    foreach($features as $feature)

                    {

                        if($products_with_different_attribute && $feature['id_product_attribute'])

                            $attribute_includes[] = $feature['id_product_attribute'];

                        else

                            $product_includes[] = $feature['id_product'];

                    }

                }

                else

                    $ok = false; // return false;

            }

            if($ok && !$condition['apply_all_supplier'] && $condition['select_suppliers'] && $condition['select_suppliers']!='all')

            {

                $include_check = true;

                $select_suppliers = explode(',',$condition['select_suppliers']);

                $sql = 'SELECT DISTINCT cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp

                LEFT JOIN  `'._DB_PREFIX_.'product`_supplier ps ON (ps.id_product = cp.id_product AND ps.id_product_attribute=cp.id_product_attribute)

                LEFT JOIN `'._DB_PREFIX_.'supplier` su ON (su.id_supplier = ps.id_supplier);

                WHERE cp.id_cart="'.(int)$cart->id.'" AND su.id_supplier IN ('.implode(',',array_map('intval',$select_suppliers)).')';

                if($suppliers = Db::getInstance()->executeS($sql))

                {

                    foreach($suppliers as $supplier)

                    {

                        if($products_with_different_attribute && $supplier['id_product_attribute'])

                            $attribute_includes[] = $supplier['id_product_attribute'];

                        else

                            $product_includes[] = $supplier['id_product'];

                    }

                }

                else

                    $ok = false; // return false;

            }

            if($product_excludes)

                $filter .= ' AND cp.id_product NOT IN ('.implode(',',array_map('intval',$product_excludes)).')';

            if($attribute_excludes)

                $filter .= ' AND cp.id_product_attribute NOT IN ('.implode(',',array_map('intval',$attribute_excludes)).')';

            if($include_check && !$product_includes && !$attribute_includes)

                $product_includes[]=0;

            if($product_includes)

                $filter .= ' AND '.(!$attribute_includes ? 'cp.id_product IN ('.implode(',',array_map('intval',$product_includes)).')':'(cp.id_product IN ('.implode(',',array_map('intval',$product_includes)).') OR cp.id_product_attribute IN ('.implode(',',array_map('intval',$attribute_includes)).'))' ) .' ';

            elseif($attribute_includes)

                $filter .= ' AND cp.id_product_attribute IN ('.implode(',',array_map('intval',$attribute_includes)).')';

            if(!$condition['apply_all_manufacturer'] && $condition['select_manufacturers'] && $condition['select_manufacturers']!='all')

            {

                $select_manufacturers = explode(',',$condition['select_manufacturers']);

                if(!$joinProduct)

                {

                    $leftJoin .=' LEFT JOIN  `'._DB_PREFIX_.'product` p ON (p.id_product = cp.id_product)';

                    $joinProduct = true;

                }

                $filter .=' AND p.id_manufacturer IN ('.implode(',',array_map('intval',$select_manufacturers)).')';

            }

            if($condition['total_weight']!=0)

            {

                $products = Db::getInstance()->executeS('SELECT sum(cp.quantity) as total_quantity,cp.id_product FROM `'._DB_PREFIX_.'cart_product` cp

                '.($leftJoin ? :'').'

                WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY cp.id_product');

                if($ok && $products)

                {

                    $totalWeight = 0;

                    foreach($products as $product)

                    {

                        $productObj = new Product($product['id_product'],true);

                        $totalWeight += $product['total_quantity']*$productObj->weight;

                    }

                    if(!self::checkCalculator($condition['total_weight'],$totalWeight,$condition['total_weight_cal']))

                        return false;

                }

                else

                    return false;



            }

            if($condition['total_product_quantity']!=0)

            {

                $products = Db::getInstance()->executeS('SELECT sum(cp.quantity) as total_quantity,cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp

                '.($leftJoin ? :'').'

                WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY cp.id_product,cp.id_product_attribute');

                if($ok && $products)

                {

                    $totalQuantity = 0;

                    foreach($products as $product)

                    {

                        $totalQuantity += $product['total_quantity'];

                    }

                    if(!self::checkCalculator($condition['total_product_quantity'],$totalQuantity,$condition['total_product_quantity_cal']))

                        return false;

                }

                else

                    return false;



            }

            if($id_product)

            {

                return (int)Db::getInstance()->getValue('SELECT cp.id_product FROM `'._DB_PREFIX_.'cart_product` cp

                    '.($leftJoin ? :'').'

                    WHERE cp.id_cart='.(int)$cart->id.$filter.' AND cp.id_product='.(int)$id_product);



            }

            if($condition['quantity_of_same_product']!=0)

            {

                $quantity_of_same_product = $condition['quantity_of_same_product'];

                $quantity_of_same_product_cal = $condition['quantity_of_same_product_cal'];

                if($products_with_different_attribute)

                {

                    $products = Db::getInstance()->executeS('SELECT sum(cp.quantity) as total_quantity,cp.id_product,cp.id_product_attribute FROM `'._DB_PREFIX_.'cart_product` cp

                    '.($leftJoin ? :'').'

                    WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY cp.id_product,cp.id_product_attribute');

                }

                else

                {

                    $products = Db::getInstance()->executeS('SELECT sum(cp.quantity) as total_quantity,cp.id_product FROM `'._DB_PREFIX_.'cart_product` cp

                    '.($leftJoin ? :'').'

                    WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY cp.id_product');

                }

                if($ok && $products)

                {

                    foreach($products as $product)

                    {

                        if(!self::checkCalculator($quantity_of_same_product,$product['total_quantity'],$quantity_of_same_product_cal))

                            return false;

                    }



                }

                else

                    return false;

            }

            if($condition['number_of_different_product']!=0)

            {

                $number_of_different_product = $condition['number_of_different_product'];

                $number_of_different_product_cal = $condition['number_of_different_product_cal'];

                $quantity  = (int)Db::getInstance()->getValue('SELECT '.($products_with_different_attribute ?  'COUNT(DISTINCT cp.id_product,cp.id_product_attribute)':'COUNT(DISTINCT cp.id_product)').' FROM `'._DB_PREFIX_.'cart_product` cp

                '.($leftJoin ? :'').'

                WHERE cp.id_cart='.(int)$cart->id.$filter);

                if(!$ok || !self::checkCalculator($number_of_different_product,$quantity,$number_of_different_product_cal))

                    return false;

            }

            if($condition['number_of_product_in_same_category']!=0)

            {

                $number_of_product_in_same_category = (int)$condition['number_of_product_in_same_category'];

                $number_of_product_in_same_category_cal = $condition['number_of_product_in_same_category_cal'];

                if(!$joinProduct)

                {

                    $leftJoin .=' LEFT JOIN  `'._DB_PREFIX_.'product` p ON (p.id_product = cp.id_product)';

                    $joinProduct = true;

                }

                if($products_with_different_attribute)

                {

                    $products = Db::getInstance()->executeS('SELECT COUNT(DISTINCT cp.id_product,cp.id_product_attribute) as total_quantity,p.id_category_default FROM `'._DB_PREFIX_.'cart_product` cp

                    '.($leftJoin ? :'').'

                    WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY p.id_category_default');

                }

                else

                {

                    $products = Db::getInstance()->executeS('SELECT COUNT(DISTINCT cp.id_product) as total_quantity,p.id_category_default FROM `'._DB_PREFIX_.'cart_product` cp

                    '.($leftJoin ? :'').'

                    WHERE cp.id_cart='.(int)$cart->id.$filter.' GROUP BY p.id_category_default');

                }

                if($ok && $products)

                {

                    foreach($products as $product)

                    {

                        if(!self::checkCalculator($number_of_product_in_same_category,$product['total_quantity'],$number_of_product_in_same_category_cal))

                            return false;

                    }

                }

                else

                    return false;

            }

            return true;

        }



    }

    public function checkSpecificOccasion($condition)

    {

        $specific_occasion = $condition['specific_occasion'];

        if($specific_occasion=='hour_of_day')

        {

            $hour_of_day_from = Tools::jsonDecode($condition['specific_occasion_hour_of_day_from'],true);

            $hour_of_day_to  = Tools::jsonDecode($condition['specific_occasion_hour_of_day_to'],true);

            if($hour_of_day_from)

            {

                $check = false;

                foreach($hour_of_day_from as $index => $from)

                {

                    $to = isset($hour_of_day_to[$index]) ? $hour_of_day_to[$index] :'';

                    if($from!=='' || $to!=='')

                    {

                        if((($from!=='' && (int)$from <= date('G'))|| $from==='') && (($to!=='' && (int)$to >= date('G'))|| $to===''))

                        {

                            $check = true;

                        }

                    }

                }

                if(!$check)

                    return false;

            }



        }

        if($specific_occasion=='day_of_week')

        {

            $specific_occasion_day_of_week = Tools::jsonDecode($condition['specific_occasion_day_of_week'],true);

            $specific_occasion_day_of_week_from  = Tools::jsonDecode($condition['specific_occasion_day_of_week_from'],true);

            $specific_occasion_day_of_week_to  = Tools::jsonDecode($condition['specific_occasion_day_of_week_to'],true);

            if($specific_occasion_day_of_week)

            {

                $check = false;

                foreach($specific_occasion_day_of_week as $index=>$day)

                {

                    $from = isset($specific_occasion_day_of_week_from[$index]) ? $specific_occasion_day_of_week_from[$index]:'';

                    $to = isset($specific_occasion_day_of_week_to[$index]) ? $specific_occasion_day_of_week_to[$index]:'';

                    if($day == date('N') && (($from!=='' && (int)$from <= date('G'))|| $from==='') && (($to!=='' && (int)$to >= date('G'))|| $to===''))

                    {

                        $check = true;

                    }

                }

                if(!$check)

                    return false;

            }

        }

        if($specific_occasion=='month_of_year')

        {

            $specific_occasion_month_of_year = Tools::jsonDecode($condition['specific_occasion_month_of_year'],true);

            $specific_occasion_month_of_year_from  = Tools::jsonDecode($condition['specific_occasion_month_of_year_from'],true);

            $specific_occasion_month_of_year_to  = Tools::jsonDecode($condition['specific_occasion_month_of_year_to'],true);

            if($specific_occasion_month_of_year)

            {

                $check = false;

                foreach($specific_occasion_month_of_year as $index=>$month)

                {

                    $from = isset($specific_occasion_month_of_year_from[$index]) ? $specific_occasion_month_of_year_from[$index]:'';

                    $to = isset($specific_occasion_month_of_year_to[$index]) ? $specific_occasion_month_of_year_to[$index]:'';

                    if($month == date('n') && (($from && $from <= date('d') )|| !$from) && (($to && $to >= date('d'))|| !$to))

                    {

                        $check = true;

                    }

                }

                if(!$check)

                    return false;

            }

        }

        if($specific_occasion=='from_to')

        {

            $specific_occasion_date_from  = Tools::jsonDecode($condition['specific_occasion_date_from'],true);

            $specific_occasion_date_to  = Tools::jsonDecode($condition['specific_occasion_date_to'],true);

            $check = false;

            foreach($specific_occasion_date_from as $index=>$from)

            {

                $to  = isset($specific_occasion_date_to[$index]) ? $specific_occasion_date_to[$index]:'';

                if($from || $to)

                {

                    if((($from && strtotime($from) <= strtotime(date('Y-m-d H:i:s')) )|| !$from) && (($to && strtotime($to) >= strtotime(date('Y-m-d H:i:s')))|| !$to))

                    {

                        $check = true;

                    }

                }

            }

            if(!$check)

                return false;

        }

        return true;

    }

    public function checkCoditions($id_carrier)

    {

        $conditions = Db::getInstance()->executeS('SELECT * FROM`'._DB_PREFIX_.'ets_sc_condition_rule`WHERE id_ets_sc_shipping_rule='.(int)$this->id);

        if($conditions)

        {

            foreach($conditions as $condition)

            {

                switch($condition['parent_codition'])

                {

                    case 'specific_customer':

                        $conditionSpecific = $this->checkSpecificCustomer($condition);

                        if(!$conditionSpecific && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionSpecific && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'customer_group':

                        $conditionCustomerGroup = $this->checkGroupCustomer($condition);

                        if(!$conditionCustomerGroup && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionCustomerGroup && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'customer_membership':

                        $conditionMemberShipCustomer = $this->checkMemberShipCustomer($condition);

                        if(!$conditionMemberShipCustomer && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionMemberShipCustomer && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'cart_amount':

                        $conditionCartAmount = $this->checkCartAmount($condition,$id_carrier);

                        if(!$conditionCartAmount && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionCartAmount && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'product_in_cart':

                        $conditionProductInCart = $this->checkProductInCart($condition);

                        if(!$conditionProductInCart && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionProductInCart && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'specific_occasion':

                        $conditionSpecificOccasion = $this->checkSpecificOccasion($condition);

                        if(!$conditionSpecificOccasion && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionSpecificOccasion && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'delivery_zone':

                        $conditionDeliveryZone = $this->checkDeliveryZone($condition);

                        if(!$conditionDeliveryZone && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionDeliveryZone && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'delivery_country':

                        $conditionDeliveryCountry = $this->checkDeliveryCountry($condition);

                        if(!$conditionDeliveryCountry && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionDeliveryCountry && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'delivery_state':

                        $conditionDeliveryState = $this->checkDeliveryState($condition);

                        if(!$conditionDeliveryState && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionDeliveryState && $this->type_combine_condition=='or')

                            return true;

                        break;

                    case 'delivery_zipcode':

                        $conditionDeliveryZipCode = $this->checkDeliveryZipcode($condition);

                        if(!$conditionDeliveryZipCode && $this->type_combine_condition=='and')

                            return false;

                        elseif($conditionDeliveryZipCode && $this->type_combine_condition=='or')

                            return true;

                        break;

                }

            }

        }

        return $this->type_combine_condition =='and' ? true : false;

    }

    public function checkDeliveryZipcode($condition)

    {

        $context = Context::getContext();

        if(($id_address = $context->cart->id_address_delivery) && ($address = new Address($id_address)) && Validate::isLoadedObject($address) )

        {

            $zipcode = trim($address->postcode);

            $delivery_zipcode_type = $condition['delivery_zipcode_type'] ? Tools::jsonDecode($condition['delivery_zipcode_type'],true):array();

            if($delivery_zipcode_type)

            {

                $delivery_zipcode_from = $condition['delivery_zipcode_from'] ? Tools::jsonDecode($condition['delivery_zipcode_from'],true):array();

                $delivery_zipcode_to = $condition['delivery_zipcode_to'] ? Tools::jsonDecode($condition['delivery_zipcode_to'],true):array();

                $delivery_zipcode_start_from = $condition['delivery_zipcode_start_from'] ? Tools::jsonDecode($condition['delivery_zipcode_start_from'],true):array();

                $delivery_zipcode_end_at = $condition['delivery_zipcode_end_at'] ? Tools::jsonDecode($condition['delivery_zipcode_end_at'],true):array();

                $delivery_zipcode_is_exactly = $condition['delivery_zipcode_is_exactly'] ? Tools::jsonDecode($condition['delivery_zipcode_is_exactly'],true):array();

                $delivery_zipcode_different = $condition['delivery_zipcode_different'] ? Tools::jsonDecode($condition['delivery_zipcode_different'],true):array();

                foreach($delivery_zipcode_type as $index=>$zipcode_type)

                {

                    if($zipcode_type=='from_to')

                    {

                        $from = isset($delivery_zipcode_from[$index]) ? $delivery_zipcode_from[$index]:'';

                        $to = isset($delivery_zipcode_to[$index]) ? $delivery_zipcode_to[$index] :'';

                        if(($from <= (int)$zipcode || !$from) && ($to >= (int)$zipcode || !$to))

                        {

                            return true;

                        }

                    }

                    elseif($zipcode_type=='start_from')

                    {

                        $start_from = trim(isset($delivery_zipcode_start_from[$index]) ? $delivery_zipcode_start_from[$index]:'');

                        if($start_from && Tools::strpos($zipcode,$start_from)===0)

                            return true;

                    }

                    elseif($zipcode_type=='end_at')

                    {

                        $end_at = trim(isset($delivery_zipcode_end_at[$index]) ? $delivery_zipcode_end_at[$index]:'');

                        if($end_at && Tools::strpos($zipcode.' ',$end_at.' ')!==false)

                            return true;

                    }

                    elseif($zipcode_type=='is_exactly')

                    {

                        $is_exactly = trim(isset($delivery_zipcode_is_exactly[$index]) ? $delivery_zipcode_is_exactly[$index]:'');

                        if($is_exactly && $zipcode==$is_exactly)

                            return true;

                    }

                    elseif($zipcode_type=='different')

                    {

                        $different = trim(isset($delivery_zipcode_different[$index]) ? $delivery_zipcode_different[$index]:'');

                        if($different && $zipcode!=$different)

                            return true;

                    }

                }

            }

        }

        return false;

    }

    public function checkDeliveryState($condition)

    {

        $id_states = $condition['delivery_state'] ? explode(',',$condition['delivery_state']):array();

        $context = Context::getContext();

        if(($id_address = $context->cart->id_address_delivery) && ($address = new Address($id_address)) && Validate::isLoadedObject($address))

        {

            $id_state = $address->id_state;

            return in_array($id_state,$id_states);

        }

        return false;

    }

    public function checkDeliveryCountry($condition)

    {

        $id_countries = $condition['delivery_country'] ? explode(',',$condition['delivery_country']):array();

        $context = Context::getContext();

        if(($id_address = $context->cart->id_address_delivery) && ($address = new Address($id_address)) && Validate::isLoadedObject($address))

        {

            $id_country = (int)$address->id_country;

        }

        elseif($context->country->id)

        {

            $id_country = (int)$context->country->id;

        }

        else

        {

            $id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');

        }

        return in_array($id_country,$id_countries);

    }

    public function checkDeliveryZone($condition)

    {

        $id_zones = $condition['delivery_zone'] ? explode(',',$condition['delivery_zone']) : array();

        $context = Context::getContext();

        if(($id_address = $context->cart->id_address_delivery) && ($address = new Address($id_address)) && Validate::isLoadedObject($address))

        {

            $id_zone = (int)Address::getZoneById((int)$id_address);

        }

        elseif($context->country->id)

        {

            $id_zone = (int)$context->country->id_zone;

        }

        else

        {

            $default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));

            $id_zone = (int) $default_country->id_zone;

        }

        return in_array($id_zone,$id_zones);

    }

    public function checkProductInCondition($product)

    {

        $conditions = Db::getInstance()->executeS('SELECT * FROM`'._DB_PREFIX_.'ets_sc_condition_rule`WHERE id_ets_sc_shipping_rule='.(int)$this->id.' AND parent_codition="product_in_cart"');

        if($conditions)

        {

            foreach($conditions as $condition)

                if(!$this->checkProductInCart($condition,$product['id_product']))

                    return false;

        }

        return true;

    }

    public function getPackageShippingCost($id_carrier,$use_tax,&$fees_shipping,$shipping_default)

    {

        $carrier = new Carrier($id_carrier);

        $context = Context::getContext();

        if(defined('_PS_ADMIN_DIR_') && isset($context->employee) && $context->employee->id)

            return false;

        $id_carriers = explode(',',$this->id_carriers);

        if(!isset($context->cart) ||  (!in_array('all',$id_carriers) && !in_array($carrier->id_reference,$id_carriers)))

            return false;

        if($this->checkCoditions($id_carrier))

        {

            $action = Ets_sc_action_rule::getActionByIdRule($this->id);

            if($action->type_action=='free')

            {

                $fees_shipping =0;

                if($use_tax)

                    $this->addRuleToCart(Context::getContext()->cart->id,$id_carrier,$shipping_default,$shipping_default,$action->type_action);

                return true;

            }

            else

            {

                $rule_price = 0;

                switch ($action->calcalate_cost_by)

                {

                    case 'percent_shipping_cost':

                        $rule_price = $action->fees_percent*$shipping_default/100;

                        if($action->fees_max >0 && $action->fees_max < $rule_price)

                            $rule_price = $action->fees_max;

                    break;

                    case 'percent_product_price':

                        $products = Context::getContext()->cart->getProducts();

                        if($products)

                        {

                            foreach($products as $product)

                            {

                                if((!$action->ignore_product_discounted || !$product['specific_prices']) && ($action->cal_percent_from=='all_product' || $this->checkProductInCondition($product)))

                                {

                                    $rule_price += ($action->exclude_product_tax ? $product['total'] : $product['total_wt'])*$action->fees_percent/100;

                                }

                            }

                        }

                        if($action->fees_max >0 && $action->fees_max < $rule_price)

                            $rule_price = $action->fees_max;

                    break;

                    case 'fixed_amount':

                        $rule_price = Tools::convertPrice($action->fees_amount);

                    break;

                    case 'formula':

                        $formular = Tools::strtolower($action->formular);

                        $replace = array(

                            'totalproductamounttaxincl' => 0,

                            'totalproductamounttaxexcl' => 0,

                            'totalquantity' => 0,

                            'totalweight' => 0,

                            'defaultshipping'=>$shipping_default

                        );

                        $products = Context::getContext()->cart->getProducts();

                        if($products)

                        {

                            foreach($products as $product)

                            {

                                $replace['totalproductamounttaxincl'] += $product['total_wt'];

                                $replace['totalproductamounttaxexcl'] += $product['total'];

                                $replace['totalquantity'] += $product['quantity'];

                                $replace['totalweight'] += $product['weight'] * $product['quantity'];

                            }

                        }

                        $formular = str_replace(array_keys($replace),$replace,$formular);

                        try {

                            if (function_exists('ets_sc_execute_php'))

                            {

                                $rule_price = call_user_func('ets_sc_execute_php', '$rule_price = '.$formular.';');

                                $rule_price = Tools::convertPrice(Tools::convertPrice($rule_price,new Currency($action->id_currency),false));

                            }

                        } catch (Throwable $t) {

                            $rule_price = null;

                            unset($t);

                        }

                    break;

                }

                if($rule_price!==null)

                {

                    if($use_tax && $action->id_tax_rule_group)

                    {

                        $ps_tax_address_type = Configuration::get('PS_TAX_ADDRESS_TYPE');

                        if ($ps_tax_address_type == 'id_address_invoice') {

                            $id_address = (int)Context::getContext()->cart->id_address_invoice;

                        } else {

                            $id_address = (int)Context::getContext()->cart->id_address_delivery;

                        }

                        if($id_address)

                            $address = new Address($id_address);

                        else

                            $address = new Address();

                        $address = Address::initialize($address->id,true);

                        $tax_manager = TaxManagerFactory::getManager($address, $action->id_tax_rule_group);

                        $product_tax_calculator = $tax_manager->getTaxCalculator();

                        $rule_price = $product_tax_calculator->addTaxes($rule_price);

                    }

                    if($action->type_action=='increase')

                        $fees_shipping +=$rule_price;

                    elseif($action->type_action=='decrease')

                    {

                        if($rule_price > $fees_shipping)

                            $rule_price =  $fees_shipping;

                        $fees_shipping -=$rule_price;

                    }

                    else

                        $fees_shipping = $rule_price;

                }

                if($fees_shipping<0)

                    $fees_shipping =0;

                if($use_tax)

                    $this->addRuleToCart(Context::getContext()->cart->id,$id_carrier,$rule_price,$shipping_default,$action->type_action);

                return true;

            }

        }

        return false;

    }

    public function addRuleToCart($id_cart,$id_carrier,$rule_price,$shipping_cosst,$type)

    {

        if($id_cart && $id_carrier)

        {

            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_sc_cart_rule` WHERE id_cart="'.(int)$id_cart.'" AND id_carrier="'.(int)$id_carrier.'"'))

            {

                $sql = 'UPDATE `'._DB_PREFIX_.'ets_sc_cart_rule` SET id_ets_sc_shipping_rule="'.(int)$this->id.'", shipping_price_default ="'.(float)$shipping_cosst.'",rule_price ="'.(float)$rule_price.'",type="'.pSQL($type).'" WHERE id_cart="'.(int)$id_cart.'" AND id_carrier="'.(int)$id_carrier.'"';

            }

            else

                $sql ='INSERT INTO `'._DB_PREFIX_.'ets_sc_cart_rule` (id_cart,id_carrier,id_ets_sc_shipping_rule,shipping_price_default,rule_price,type) VALUES("'.(int)$id_cart.'","'.(int)$id_carrier.'","'.(int)$this->id.'","'.(float)$shipping_cosst.'","'.(float)$rule_price.'","'.pSQL($type).'")';

            Db::getInstance()->execute($sql);

        }



    }

    public function addToOrder($order)

    {

        $order_carriers = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'order_carrier` WHERE id_order='.(int)$order->id);

        $id_carriers = array();

        if($order_carriers)

        {

            foreach($order_carriers as $order_carrier)

            {

                if(($cart_rule = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_sc_cart_rule` WHERE id_cart="'.(int)$order->id_cart.'" AND id_carrier="'.(int)$order_carrier['id_carrier'].'"')))

                {

                    $id_carriers[] = $order_carrier['id_carrier'];

                    $data = array(

                        'id_order'=>(int)$order->id,

                        'id_ets_sc_shipping_rule'=>(int)$cart_rule['id_ets_sc_shipping_rule'],

                        'id_carrier'=> (int)$order_carrier['id_carrier'],

                        'shipping_price_default' => (float)$cart_rule['shipping_price_default'],

                        'shipping_price'=>(float)$order_carrier['shipping_cost_tax_incl'],

                        'rule_price' => (float)$cart_rule['rule_price'],

                        'type'=> pSQL($cart_rule['type']),

                    );

                    Db::getInstance()->insert('ets_sc_order_rule',$data);



                    if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'orders` WHERE id_customer="'.(int)$order->id_customer.'" AND id_order< '.(int)$order->id))

                    {

                        $ruleObj = new Ets_sc_shipping_rule($cart_rule['id_ets_sc_shipping_rule']);

                        $ruleObj->new_customer++;

                        $ruleObj->update();

                    }

                }

            }

        }

        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_sc_cart_rule` WHERE id_cart="'.(int)$order->id_cart.'" '.($id_carriers ? ' AND id_carrier NOT IN ('.implode(',',array_map('intval',$id_carriers)).')':''));

    }

    public static function getPromoteDiscountRulesByPosition($position='',$way_to_promote='')

    {

         $id_lang = (int)Context::getContext()->language->id;

         $id_shop = (int)Context::getContext()->shop->id;

         $sql ='SELECT p.*,pl.*,r.*,rl.name,a.* FROM  `'._DB_PREFIX_.'ets_sc_shipping_rule` r

         INNER JOIN  `'._DB_PREFIX_.'ets_sc_action_rule` a ON (r.id_ets_sc_shipping_rule = a.id_ets_sc_shipping_rule)

         INNER JOIN  `'._DB_PREFIX_.'ets_sc_promote_rule` p ON (r.id_ets_sc_shipping_rule = p.id_ets_sc_shipping_rule'.($position ? ' AND (p.position_to_display LIKE  "%'.pSQL($position).'%" OR p.position_to_display="all")':'').($way_to_promote ? ' AND p.way_to_promote="'.pSQL($way_to_promote).'"':'').' )

         LEFT JOIN  `'._DB_PREFIX_.'ets_sc_promote_rule_lang` pl ON (p.id_ets_sc_promote_rule = pl.id_ets_sc_promote_rule AND pl.id_lang="'.(int)$id_lang.'")

         LEFT JOIN  `'._DB_PREFIX_.'ets_sc_shipping_rule_lang` rl ON (r.id_ets_sc_shipping_rule=rl.id_ets_sc_shipping_rule AND rl.id_lang="'.(int)$id_lang.'")

         WHERE r.active=1 AND p.enabled=1 AND r.id_shop = "'.(int)$id_shop.'" GROUP BY p.id_ets_sc_promote_rule';

         $rules = Db::getInstance()->executeS($sql);

         if($rules)

         {

            foreach($rules as $key=>$rule)

            {

                if($rule['way_to_promote']=='display_notification' || $rule['way_to_promote']=='display_banner')

                {

                    if(!self::checkHookDisplay($rule['id_ets_sc_promote_rule']))

                    {

                        unset($rules[$key]);

                        continue;

                    }

                }

                if($rule['way_to_promote']=='display_notification')

                    $rules[$key]['notification'] = trim(self::replaceAvailableTags($rules[$key]['notification'],$rule),"\n");

                elseif($rule['way_to_promote']=='display_popup')

                    $rules[$key]['content_popup'] = self::replaceAvailableTags($rules[$key]['content_popup'],$rule);

                elseif($rule['way_to_promote']=='display_highlight_bar')

                    $rules[$key]['content_bar'] = self::replaceAvailableTags($rules[$key]['content_bar'],$rule);

            }

         }

         return $rules;

    }

    public static function checkHookDisplay($id_ets_sc_promote_rule)

    {

        $controller = Tools::getValue('controller');

        if($controller=='category' || $controller=='product')

        {

            $promote = new Ets_sc_promote_rule($id_ets_sc_promote_rule);

            if($controller=='category')

            {

                if($promote->applicable_product_categories=='all_product')

                    return true;

                else

                {

                    $id_category = (int)Tools::getValue('id_category');

                    if($promote->applicable_categories)

                    {

                       $id_categories = explode(',',$promote->applicable_categories);

                       if(in_array($id_category,$id_categories))

                            return true;

                       if($promote->include_sub_categories)

                       {

                            if(Db::getInstance()->getRow('SELECT id_category FROM  `'._DB_PREFIX_.'category` WHERE id_parent IN ('.implode(',',array_map('intval',$id_categories)).') AND id_category='.(int)$id_category))

                                return true;

                       }

                    }

                    return false;

                }

            }

            if($controller=='product')

            {

                $id_product = (int)Tools::getValue('id_product');

                if($promote->exclude_products);

                {

                    $exclude_products = explode(',',$promote->exclude_products);

                    if($exclude_products)

                    {

                        foreach($exclude_products as $exdlude_product)

                        {

                            $ids = explode('-',$exdlude_product);

                            if($ids[0] == $id_product)

                                return false;

                        }

                    }

                }

                if($promote->applicable_product_categories=='all_product')

                {

                    return true;

                }

                else

                {

                    if($promote->include_specific_products);

                    {

                        $include_products = explode(',',$promote->include_specific_products);

                        if($include_products)

                        {

                            foreach($include_products as $include_product)

                            {

                                $ids = explode('-',$include_product);

                                if($ids[0] == $id_product)

                                    return true;

                            }

                        }

                    }

                    if($promote->applicable_categories)

                    {

                        $id_categories = explode(',',$promote->applicable_categories);

                        $sql = 'SELECT p.id_product FROM  `'._DB_PREFIX_.'product` p 

                        LEFT JOIN  `'._DB_PREFIX_.'category_product` cp ON (cp.id_product= p.id_product)

                        LEFT JOIN  `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)

                        WHERE p.id_product = "'.(int)$id_product.'" AND (c.id_category IN ('.implode(',',array_map('intval',$id_categories)).') '.($promote->include_sub_categories ? ' OR c.id_parent IN ('.implode(',',array_map('intval',$id_categories)).')':'').' )';

                        if(Db::getInstance()->getRow($sql))

                            return true;

                    }



                }

                return false;

            }

        }

        return true;

    }

    public static function replaceAvailableTags($content,$rule)

    {

        $content = Tools::nl2br($content);

        $replace = array(

            '[shipping_rule]' => $rule['name'],

            '[value]' => self::getValueDiscountRule($rule),

            '[carrier]' => Ets_sc_shipping_rule::getInstance()->getListCarriers($rule['id_carriers'])

        );

        return str_replace(array_keys($replace),$replace,$content);

    }

    public static function getValueDiscountRule($rule)

    {

        $calcalate_cost_by = $rule['calcalate_cost_by'];

        if($calcalate_cost_by=='percent_shipping_cost' || $calcalate_cost_by=='percent_product_price')

            return $rule['fees_percent'].'%';

        elseif($calcalate_cost_by=='fixed_amount')

            return Tools::displayPrice(Tools::convertPrice($rule['fees_amount']));

        return '';

    }

    public function getListCarriers($id_carriers)

    {

        if($id_carriers)

        {

            if($id_carriers=='all')

                return $this->l('All carriers');

            else

            {

                $carriers = Ets_sc_shipping_rule::getCarriers(' AND c.id_reference IN('.implode(',',array_map('intval',explode(',',$id_carriers))).')');

                $list_carriers = '';

                if($carriers)

                {

                    foreach($carriers as $carrier)

                    {

                        $list_carriers .= $carrier['name'].', ';

                    }

                }

                return trim($list_carriers,', ');

            }

        }



    }

    public static function getRuleReports($filter='',$sort='',$start=0,$limit=10,$total=false)

    {

        $id_lang = (int)Context::getContext()->language->id;

        $id_shop = (int)Context::getContext()->shop->id;

        $order_status = Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED') ? explode(',',Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED')):false;

        if($total)

            $sql ='SELECT COUNT(DISTINCT r.id_ets_sc_shipping_rule) FROM  `'._DB_PREFIX_.'ets_sc_shipping_rule` r';

        else

            $sql ='SELECT r.*,rl.name,count(o.id_order) as total_order, SUM(o.total_paid/c.conversion_rate) as total_amount FROM  `'._DB_PREFIX_.'ets_sc_shipping_rule` r';

        $sql .=' INNER JOIN  `'._DB_PREFIX_.'ets_sc_order_rule` ocr ON (ocr.id_ets_sc_shipping_rule=r.id_ets_sc_shipping_rule)

        LEFT JOIN  `'._DB_PREFIX_.'orders` o ON (o.id_order = ocr.id_order '.($order_status ? ' AND o.current_state IN ('.implode(',',array_map('intval',$order_status)).')':'').')

        LEFT JOIN  `'._DB_PREFIX_.'currency` c ON (c.id_currency = o.id_currency)

        LEFT JOIN  `'._DB_PREFIX_.'ets_sc_shipping_rule_lang` rl ON (r.id_ets_sc_shipping_rule=rl.id_ets_sc_shipping_rule AND rl.id_lang="'.(int)$id_lang.'")

        WHERE r.id_shop= "'.(int)$id_shop.'" '.($filter ? $filter: '');

        if($total)

            return Db::getInstance()->getValue($sql);

        else

        {

            $sql .=' GROUP BY r.id_ets_sc_shipping_rule '.($sort ? ' ORDER BY '.$sort: ' ORDER BY r.id_ets_sc_shipping_rule asc').' LIMIT '.(int)$start.','.(int)$limit.'';

            return Db::getInstance()->executeS($sql);

        }

    }

    public function getOrderApplied()

    {

        $order_status = Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED') ? explode(',',Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED')):false;

        $sql = 'SELECT o.id_order,o.id_customer,o.reference,o.total_paid,o.date_add,c.email,c.firstname,c.lastname,cu.id_currency,ocr.shipping_price_default,ocr.shipping_price,ocr.rule_price,ocr.type FROM  `'._DB_PREFIX_.'orders` o 

        INNER JOIN  `'._DB_PREFIX_.'ets_sc_order_rule` ocr ON (o.id_order=ocr.id_order)

        LEFT JOIN  `'._DB_PREFIX_.'currency` cu ON (cu.id_currency = o.id_currency)

        LEFT JOIN  `'._DB_PREFIX_.'customer` c ON (c.id_customer = o.id_customer)

        WHERE ocr.id_ets_sc_shipping_rule='.(int)$this->id.($order_status ? ' AND o.current_state IN ('.implode(',',array_map('intval',$order_status)).')':'');

        $orders = Db::getInstance()->executeS($sql);

        if($orders)

        {

            foreach($orders as &$order)

            {

                $currency = new Currency($order['id_currency']);

                $order['shipping_price_default'] = $order['shipping_price_default']!=0 ? Tools::displayPrice($order['shipping_price_default'],$currency): $this->l('Free shipping');

                $order['shipping_price'] = $order['shipping_price']!=0 ? Tools::displayPrice($order['shipping_price'],$currency): $this->l('Free shipping');

                $order['rule_price'] = Tools::displayPrice($order['rule_price'],$currency);

                $order['link_view'] = Ets_shippingcost::getLinkOrderAdmin($order['id_order']);

                $order['view_customer'] = Ets_shippingcost::getLinkCustomerAdmin($order['id_customer']);

                $order['products'] = $this->printOrderProducts($order['id_order']);

            }

        }

        return $orders;

    }

    public static function getCzfProductByProductId($idProduct, $idLang = null)

    {

        $sql = "SELECT cp.logo

                FROM `"._DB_PREFIX_."ets_czf_product` cp 

                WHERE cp.id_product =".(int)$idProduct;

        if($idLang)

        {

            return Db::getInstance()->getRow($sql);

        }

    }

    public function printOrderProducts($id_order)

    {

        $products = Db::getInstance()->executeS('SELECT product_name,product_id,product_attribute_id,sum(product_quantity) as product_quantity FROM `'._DB_PREFIX_.'order_detail` WHERE id_order='.(int)$id_order.' GROUP BY product_id,product_attribute_id ORDER BY id_order_detail ASC' );

        if($products)

        {

            foreach($products as &$product)

            {

                $product_class = new Product($product['product_id'],false,Context::getContext()->language->id);

                $image=false;

                if($product['product_attribute_id'])

                {

                    $sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attribute_image` pai

                    INNER JOIN `'._DB_PREFIX_.'image` i ON pai.id_image=i.id_image WHERE pai.id_product_attribute='.(int)$product['product_attribute_id'];

                    if(!$image = Db::getInstance()->getRow($sql.' AND i.cover=1'))

                        $image  = Db::getInstance()->getRow($sql);

                }

                if(!$image)

                {

                    $sql = 'SELECT i.id_image FROM `'._DB_PREFIX_.'image` i';

                    if($product['product_attribute_id'])

                        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.id_image=pai.id_image AND pai.id_product_attribute="'.(int)$product['product_attribute_id'].'")';

                    $sql .= ' WHERE i.id_product="'.(int)$product['product_id'].'"';

                    if(!$image = Db::getInstance()->getRow($sql.' AND i.cover=1'))

                    {

                        $image = Db::getInstance()->getRow($sql);

                    }

                }



                if($image)

                {

                    if(version_compare(_PS_VERSION_, '1.7', '>='))

                        $type_image= ImageType::getFormattedName('small');

                    else

                        $type_image= ImageType::getFormatedName('small');

                    $product['image'] =  Context::getContext()->link->getImageLink($product_class->link_rewrite,$image['id_image'],$type_image);

                }

                else

                {

                    $product['image'] = '';

                }

                if(Module::isEnabled('ets_customfields'))

                {

                    if(($czfProduct = self::getCzfProductByProductId($product_class->id,Context::getContext()->language->id)) && $czfProduct['logo'])

                    {

                        $product['image'] = Context::getContext()->link->getMediaLink(_PS_IMG_.'../'.$czfProduct['logo']); ;

                    }

                }

            }

        }

        Context::getContext()->smarty->assign(

            array(

                'products' => $products,

                'link'=> Context::getContext()->link,

            )

        );

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_shippingcost/views/templates/hook/order_products.tpl');

    }

    public function getTotaldiscountApplied()

    {

        $order_status = Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED') ? explode(',',Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED')):false;

        $sql = 'SELECT COUNT(DISTINCT o.id_order) FROM  `'._DB_PREFIX_.'orders` o 

        INNER JOIN  `'._DB_PREFIX_.'ets_sc_order_rule` ocr ON (o.id_order=ocr.id_order)

        WHERE ocr.id_ets_sc_shipping_rule='.(int)$this->id.($order_status ? ' AND o.current_state IN ('.implode(',',array_map('intval',$order_status)).')':'');

        return Db::getInstance()->getValue($sql);

    }

    public function getTotalOrderAmount()

    {

        $order_status = Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED') ? explode(',',Configuration::get('ETS_SC_STATUS_ORDER_VALIDATED')):false;

        $sql = 'SELECT sum(o.total_paid/c.conversion_rate) FROM  `'._DB_PREFIX_.'orders` o 

        INNER JOIN  `'._DB_PREFIX_.'ets_sc_order_rule` ocr ON (o.id_order=ocr.id_order)

        LEFT JOIN  `'._DB_PREFIX_.'currency` c ON (c.id_currency = o.id_currency)

        WHERE ocr.id_ets_sc_shipping_rule='.(int)$this->id.($order_status ? ' AND o.current_state IN ('.implode(',',array_map('intval',$order_status)).')':'');

        return (float)Db::getInstance()->getValue($sql);

    }

}
