<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Company extends Model
{
    use Billable;

    public $table = "companies";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
		'id',
		'name',
		'description',
		'industry',
		'subscription_tier',
		'number_of_staff',
		'active',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
    ];

    protected $appends = ['actual_number_of_staff'];

    protected $hidden = ['card_brand', 'card_last_four'];


    // model events - similar to Yii2 beforeSave, afterSave etc...
    public static function boot()
    {
        parent::boot();

        self::retrieved(function($company){
            // ... code here
        });

        self::creating(function($company){
            // ... code here

            if (isset($company->active) && $company->isDirty('active')) {
                if ($company->active === "on" || $company->active === 1) {
                    $company->active = 1;
                } else {
                    $company->active = false;
                }
            }
        });

        self::created(function($company){
            // ... code here
        });

        self::updating(function($company){
            // ... code here
        });

        self::updated(function($company){
            // ... code here
        });

        self::deleting(function($company){
            // ... code here
        });

        self::deleted(function($company){
            // ... code here
        });
    }




    public static $rules = [
        // create rules
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * Get the number of staff who belong to this company
     *
     * @return  integer
     */
    public function getActualNumberOfStaffAttribute() {
        return $this->users()->active()->count();
    }
}
