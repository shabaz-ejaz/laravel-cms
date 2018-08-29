<?php

namespace App\Models;

use App\Models\Role;
use Laravel\Cashier\Billable;
use App\Models\UserMeta;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Billable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'company_id',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
        'referred_by',
        'referral_code',
        'approved_referrer',
        'approved_referrer_date',
        'referral_code_redeem_date',
        'discount_rate',
        'discount_rate_percentage',
        'commission_rate'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'card_brand', 'card_last_four'];


    // model events - similar to Yii2 beforeSave, afterSave etc...
    public static function boot()
    {
        parent::boot();

        self::retrieved(function ($user) {
        });

        self::creating(function ($user) {
        });

        self::created(function ($user) {
            // ... code here
        });

        self::updating(function ($user) {
            if (isset($user->active) && $user->isDirty('active')) {
                if ($user->active === "on" || $user->active === 1) {
                    $user->active = 1;
                } else {
                    $user->active = false;
                }
            }
        });

        self::updated(function ($user) {
            // ... code here
        });

        self::deleting(function ($user) {
            // ... code here
        });

        self::deleted(function ($user) {
            // ... code here
        });
    }
    
    

    /**
     * User UserMeta
     *
     * @return Relationship
     */
    public function meta()
    {
        return $this->hasOne(UserMeta::class);
    }

    /**
     * User Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->where('active', 1);
    }

    /**
     * User Roles
     *
     * @return Relationship
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has role
     *
     * @param  string  $role
     * @return boolean
     */
    public function hasRole($role)
    {
        $roles = array_column($this->roles->toArray(), 'name');
        return array_search($role, $roles) > -1;
    }

    /**
     * Check if user has permission
     *
     * @param  string  $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        return $this->roles->each(function ($role) use ($permission) {
            if (in_array($permission, explode(',', $role->permissions))) {
                return true;
            }
        });

        return false;
    }

    /**
     * Teams
     *
     * @return Relationship
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Team member
     *
     * @return boolean
     */
    public function isTeamMember($id)
    {
        $teams = array_column($this->teams->toArray(), 'id');
        return array_search($id, $teams) > -1;
    }

    /**
     * Team admin
     *
     * @return boolean
     */
    public function isTeamAdmin($id)
    {
        $team = $this->teams->find($id);

        if ($team) {
            return (int) $team->user_id === (int) $this->id;
        }

        return false;
    }

    /**
     * Find by Email
     *
     * @param  string $email
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function referredBy()
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referredUsers()
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    /**
     * Scope a query to only include users who are approved referrers and the referral code is matching
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $query
     * @param mixed $referral_code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReferralValid($query, $referral_code) {
        return $query->where(['referral_code' => trim($referral_code), 'approved_referrer' => 1]);
    }

    public function scopeActive($query) {
        return $query->where('active',1);
    }
}
