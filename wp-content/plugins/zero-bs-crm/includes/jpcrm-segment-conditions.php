<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V4.5
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

/*
 * This Parent class allows us to simplify what's needed of each condition into a class below
*/
class zeroBSCRM_segmentCondition {

    public $key = false;
    public $condition = false;

    // killswitch
    private $addFilters = true;


    /**
     * Jetpack CRM Segment Argument Constructor.
     */
    public function __construct($constructionArgs=array()) {

        // in children we play with the order here (preConstructor)
        // so it's separated into an init func
        $this->init($constructionArgs);

    }

    public function init($constructionArgs=array()){

        global $zbs;

        if ($this->addFilters && $this->key !== false && is_array($this->condition)){

            // __ name
            if (isset($this->condition['name'])) $this->condition['name'] = __($this->condition['name'],'zero-bs-crm');

            // add the condition
            add_filter( 'zbs_segment_conditions', array($this,'condition'));

            // add the query arg builder
            add_filter( $zbs->DAL->makeSlug($this->key).'_zbsSegmentArgumentBuild', array($this,'conditionArg'),10,2);

        }

    }

    public function condition($conditions=array()) {

        if ($this->key !== false && is_array($this->condition)) return array_merge($conditions,array($this->key => $this->condition));

        // else don't add
        return $conditions;
    }
    // note starting arg is ignored (should not have been called multiple times)
    public function conditionArg($startingArg=false,$condition=false,$conditionKeySuffix=false){

        global $zbs,$wpdb,$ZBSCRM_t;

        return $startingArg;
    }

}

function zeroBSCRM_segments_availableConditions(){

    return apply_filters('zbs_segment_conditions',array(

            'status' => array('name'=>__('Status',"zero-bs-crm"),'operators' => array('equal','notequal'),'fieldname'=>'status'),
            'fullname' => array('name'=>__('Full Name',"zero-bs-crm"),'operators' => array('equal','notequal','contains'),'fieldname'=>'fullname'),
            'email' => array('name'=>__('Email',"zero-bs-crm"),'operators' => array('equal','notequal','contains'),'fieldname'=>'email'),
            'dateadded' => array('name'=>__('Date Added',"zero-bs-crm"),'operators' => array('before','after','daterange'),'fieldname'=>'dateadded','conversion'=>'date-to-uts'),
            'datelastcontacted' => array('name'=>__('Date Last Contacted',"zero-bs-crm"),'operators' => array('before','after','daterange'),'fieldname'=>'datelastcontacted','conversion'=>'date-to-uts'),
            'tagged' => array('name'=>__('Has Tag',"zero-bs-crm"),'operators' => array('tag'),'fieldname'=>'tagged'),
            'nottagged' => array('name'=>__('Is Not Tagged',"zero-bs-crm"),'operators' => array('tag'),'fieldname'=>'nottagged'),

        ));  
}

function zeroBSCRM_segments_availableConditionOperators(){

    return array(

                    'equal' => array('name'=>__('Equals (=)', 'zero-bs-crm')),
                    'notequal' => array('name'=>__('Not equals (!=)', 'zero-bs-crm')),
                    'contains' => array('name'=>__('Contains (*)', 'zero-bs-crm')),
                    'larger' => array('name'=>__('Larger than (>)', 'zero-bs-crm')),
                    'less' => array('name'=>__('Less than (<)', 'zero-bs-crm')),
                    'before' => array('name'=>__('Before date', 'zero-bs-crm')),
                    'after' => array('name'=>__('After date', 'zero-bs-crm')),
                    'daterange' => array('name'=>__('In date range', 'zero-bs-crm')),
                    'floatrange' => array('name'=>__('In range', 'zero-bs-crm')),
                    'intrange' => array('name'=>__('In range', 'zero-bs-crm')),

                    // added for hypo, but will generally be useful
                    'istrue' => array('name'=>__('Is True', 'zero-bs-crm')),
                    'isfalse' => array('name'=>__('Is False', 'zero-bs-crm'))

            );

}