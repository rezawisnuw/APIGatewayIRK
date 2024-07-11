<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserJWT extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $connection =  "pgsqlgcp_ess";
    
    protected $table = "IDM_UserLogLogin";

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $fillable = [
    //     'personnelnumber',
    //     //'email',
    //     'password',
    // ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * Get the attributes that should be cast.
    //  *
    //  * @return array<string, string>
    //  */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getDataLogin(Request $request)
    {
      
        $nik = $request['data']['nik'];
        $pass = $request['data']['pass'];
        
        try {
            
            $data = DB::connection(config('app.URL_PGSQLGCP_ESS'))->select("select * from public.loginuser(?,?)", [$nik, $pass]);

            if(is_array($data)){
                if($data[0]->status == 'Login Berhasil' && !str_contains($data[0]->status,'menit')){
                    return [$nik,$pass];
                }
            }

            return $data;

        } catch (\Throwable $e) {
            return $e->getCode() == 0 ? 'Error Function Laravel = ' . $e->getMessage() : 'Error Database = ' . $e->getMessage();
        }
        
    }
}