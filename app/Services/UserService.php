<?php

namespace App\Services;

use DB;
use Auth;
use Mail;
use Config;
use Session;
use Exception;
use App\Models\User;
use App\Models\UserMeta;
use Grafite\Cms\Services\FileService;
use App\Models\Role;
use App\Models\Company;
use App\Events\UserRegisteredEmail;
use App\Notifications\ActivateUserEmail;
use Illuminate\Support\Facades\Schema;

class UserService
{
    /**
     * User model
     * @var User
     */
    public $model;

    /**
     * User Meta model
     * @var UserMeta
     */
    protected $userMeta;

    /**
     * Team Service
     * @var TeamService
     */
    protected $team;

    /**
     * Role Service
     * @var RoleService
     */
    protected $role;

    protected $company;


    public function __construct(User $model, UserMeta $userMeta, Role $role)
    {
        $this->model = $model;
        $this->userMeta = $userMeta;
        $this->role = $role;
        $this->company = new Company();
    }

    /**
     * Get all users
     *
     * @return array
     */
    public function all()
    {
        if (isset(request()->dir) && isset(request()->field)) {
            $model = $this->model->orderBy(request()->field, request()->dir);
        } else {
            $model = $this->model->orderBy('created_at', 'desc');
        }
        return $model->paginate(Config::get('cms.pagination', 24));
    }

    /**
     * Find a user
     * @param  integer $id
     * @return User
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Search the users
     *
     * @param  array $input
     * @return mixed
     */
    public function search($input)
    {
        $query = $this->model->orderBy('created_at', 'desc');

        if(!empty($input['id'])) {
            $query->where('id', $input['id']);
        }

        if(!empty($input['name'])) {
            $query->where('name', 'LIKE', '%'.$input['name'].'%');
        }

        if(!empty($input['email'])) {
            $query->where('email', 'LIKE', '%'.$input['email'].'%');
        }

        if(!empty($input['company'])) {
            $query->where('company_id', $input['company']);
        }

        if(!empty($input['active'])) {
            $query->where('active', 1);
        } else {
            $query->where('active', '!=', 1)->orWhereNull('active');
        }

     /*   $columns = Schema::getColumnListing('users');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$input.'%');
        };*/

        return $query->paginate(Config::get('cms.pagination', 24));
    }

    /**
     * Find a user by email
     *
     * @param  string $email
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->model->findByEmail($email);
    }

    /**
     * Find by Role ID
     * @param  integer $id
     * @return Collection
     */
    public function findByRoleID($id)
    {
        $usersWithRepo = [];
        $users = $this->model->all();

        foreach ($users as $user) {
            if ($user->roles->first()->id == $id) {
                $usersWithRepo[] = $user;
            }
        }

        return $usersWithRepo;
    }

    /**
     * Find by the user meta activation token
     *
     * @param  string $token
     * @return boolean
     */
    public function findByActivationToken($token)
    {
        $userMeta = $this->userMeta->where('activation_token', $token)->first();

        if ($userMeta) {
            return $userMeta->user();
        }

        return false;
    }

    /**
     * Create a user's profile
     *
     * @param  User $user User
     * @param  string $password the user password
     * @param  string $role the role of this user
     * @param  boolean $sendEmail Whether to send the email or not
     * @return User
     */
    public function create($user, $password, $role = 'member', $sendEmail = true)
    {
        try {
            DB::transaction(function () use ($user, $password, $role, $sendEmail) {
                $this->userMeta->firstOrCreate([
                    'user_id' => $user->id
                ]);

                $this->assignRole($role, $user->id);

                if ($sendEmail) {
                    event(new UserRegisteredEmail($user, $password));
                }

            });

            $this->setAndSendUserActivationToken($user);

            return $user;
        } catch (Exception $e) {
            throw new Exception("We were unable to generate your profile, please try again later.", 1);
        }
    }



    /**
     * Create a user's associated models
     *
     * @param  User $user User
     * @param  string $password the user password
     * @param  string $role the role of this user
     * @param  boolean $send_email Whether to send the email or not
     * @param  integer $subscription_tier The subscription tier enum key
     * @return User
     */
    public function createUserAssociatedData($input, $user, $password, $role = 'member', $send_email = true, $company_id = 0)
    {
        try {
            DB::transaction(function () use ($input, $user, $password, $role, $send_email, $company_id) {

                $this->userMeta->create($input);
                $this->assignRole($role, $user->id);

                if (!empty($company_id)) {
                    $this->joinCompany($company_id, $user->id);
                } else {

                    if (!empty($input['business_name'])) {
                        $company = $this->company->whereRaw('TRIM(LOWER(`name`)) LIKE ? ', [ trim(strtolower($input['business_name'])) ])->first();

                        // if company already exists then join company, else create new company and join
                        if (!empty($company)) {
                                $this->joinCompany($company->id, $user->id);
                        } else {
                            $company = $this->company->create(['name' => $input['business_name'], 'industry' => $input['industry'], 'number_of_staff' => $input['number_of_staff']]);
                            $this->joinCompany($company->id, $user->id);
                        }
                    }
                }


                if ($send_email) {
                    event(new UserRegisteredEmail($user, $password));
                }
            });

            $this->setAndSendUserActivationToken($user);

            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }



    /**
     * Update a user's profile
     *
     * @param  int $userId User Id
     * @param  array $inputs UserMeta info
     * @return User
     */
    public function update($userId, $payload)
    {
        try {
            return DB::transaction(function () use ($userId, $payload) {
                $user = $this->model->find($userId);


              /*  if (isset($payload['meta']['marketing']) && ($payload['meta']['marketing'] == 1 || $payload['meta']['marketing'] == 'on')) {
                    $payload['meta']['marketing'] = 1;
                } else {
                    $payload['meta']['marketing'] = 0;
                }

                if (isset($payload['meta']['terms_and_cond']) && ($payload['meta']['terms_and_cond'] == 1 || $payload['meta']['terms_and_cond'] == 'on')) {
                    $payload['meta']['terms_and_cond'] = 1;
                } else {
                    $payload['meta']['terms_and_cond'] = 0;
                }*/

                $userMetaResult = (isset($payload['meta'])) ? $user->meta->update($payload['meta']) : true;

                $user->update($payload);
                $this->updateRoles($payload, $user);
                return $user;
            });
        } catch (Exception $e) {
            throw new Exception("We were unable to update your profile", 1);
        }
    }


    // update multiple roles from select2
    private function updateRoles($payload, $user)
    {
        if (isset($payload['roles'])) {
            $this->unassignAllRoles($user->id);
            foreach ($payload['roles'] as $role) {
                $this->assignRole($role, $user->id);
            }
        }
    }


    /**
     * Invite a new member
     * @param  array $info
     * @return void
     */
    public function invite($info)
    {
        $password = substr(md5(rand(1111, 9999)), 0, 10);

        return DB::transaction(function () use ($password, $info) {
            $user = $this->model->create([
                'email' => $info['email'],
                'name' => $info['name'],
                'password' => bcrypt($password)
            ]);

            return $this->create($user, $password, $info['roles'], true);
        });
    }

    /**
     * Destroy the profile
     *
     * @param  int $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $this->unassignAllRoles($id);
                

                $userMetaResult = $this->userMeta->where('user_id', $id)->delete();
                $userResult = $this->model->find($id)->delete();

                return ($userMetaResult && $userResult);
            });
        } catch (Exception $e) {
            throw new Exception("We were unable to delete this profile", 1);
        }
    }

    /**
     * Switch user login
     *
     * @param  integer $id
     * @return boolean
     */
    public function switchToUser($id)
    {
        try {
            $user = $this->model->find($id);
            Session::put('original_user', Auth::id());
            Auth::login($user);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error logging in as user", 1);
        }
    }

    /**
     * Switch back
     *
     * @param  integer $id
     * @return boolean
     */
    public function switchUserBack()
    {
        try {
            $original = Session::pull('original_user');
            $user = $this->model->find($original);
            Auth::login($user);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error returning to your user", 1);
        }
    }

    /**
     * Set and send the user activation token via email
     *
     * @param void
     */
    public function setAndSendUserActivationToken($user)
    {
        $token = md5(str_random(40));

        $user->meta()->update([
            'activation_token' => $token
        ]);

        $user->notify(new ActivateUserEmail($token));
    }

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    /**
     * Assign a role to the user
     *
     * @param  string $roleName
     * @param  integer $userId
     * @return void
     */
    public function assignRole($roleName, $userId)
    {
        $role = $this->role->findByName($roleName);
        $user = $this->model->find($userId);

        $user->roles()->attach($role);
    }

    /**
     * Unassign a role from the user
     *
     * @param  string $roleName
     * @param  integer $userId
     * @return void
     */
    public function unassignRole($roleName, $userId)
    {
        $role = $this->role->findByName($roleName);
        $user = $this->model->find($userId);

        $user->roles()->detach($role);
    }

    /**
     * Unassign all roles from the user
     *
     * @param  string $roleName
     * @param  integer $userId
     * @return void
     */
    public function unassignAllRoles($userId)
    {
        $user = $this->model->find($userId);
        $user->roles()->detach();
    }


    /**
     * Join a company
     *
     * @param  integer $companyId
     * @param  integer $userId
     * @return void
     */
    public function joinCompany($companyId, $userId)
    {
        $company = $this->company->find($companyId);
        $user = $this->model->find($userId);

        $user->company()->associate($company);
        $user->save();
    }


    /**
     * Leave a company
     *
     * @param  integer $companyId
     * @param  integer $userId
     * @return void
     */
    public function leaveCompany($companyId, $userId)
    {
        $company = $this->company->find($companyId);
        $user = $this->model->find($userId);

        $user->company()->dissociate();
        $user->save();
    }


    /*
    |--------------------------------------------------------------------------
    | Teams
    |--------------------------------------------------------------------------
    */

    /**
     * Join a team
     *
     * @param  integer $teamId
     * @param  integer $userId
     * @return void
     */
    public function joinTeam($teamId, $userId)
    {
        $team = $this->team->find($teamId);
        $user = $this->model->find($userId);

        $user->teams()->attach($team);
    }

    /**
     * Leave a team
     *
     * @param  integer $teamId
     * @param  integer $userId
     * @return void
     */
    public function leaveTeam($teamId, $userId)
    {
        $team = $this->team->find($teamId);
        $user = $this->model->find($userId);

        $user->teams()->detach($team);
    }

    /**
     * Leave all teams
     *
     * @param  integer $teamId
     * @param  integer $userId
     * @return void
     */
    public function leaveAllTeams($userId)
    {
        $user = $this->model->find($userId);
        $user->teams()->detach();
    }
}
